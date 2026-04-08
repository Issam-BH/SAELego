<?php
require_once __DIR__ . '/../Manager/UserManager.php';
require_once __DIR__ . '/../Service/UserSession.php';
require_once __DIR__ . '/../Service/EmailService.php';
require_once __DIR__ . '/../Service/Captcha.php';
require_once __DIR__ . '/../Service/TwoFactorAuthLight.php';

class AuthController {

    public function login() {
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $captchaConfig = require __DIR__ . '/../../config/captchaConfig.php';
            $captchaService = new Captcha($captchaConfig['turnstile_secret']);
            $token = $_POST['cf-turnstile-response'] ?? '';

            if (!$captchaService->isValid($token, $_SERVER['REMOTE_ADDR'])) {
                $error = "Veuillez valider la sécurité (Captcha).";
            } else {
                $manager = new UserManager();
                $user = $manager->verifyPassword($_POST['username'], $_POST['password']);

                if ($user) {
                    if (session_status() === PHP_SESSION_NONE) session_start();
                    $_SESSION['2fa_user_id'] = $user->getIdUser();

                    if ($user->getTotpEnabled()) {
                        $_SESSION['2fa_type'] = 'totp';
                    } else {
                        $_SESSION['2fa_type'] = 'email';
                        $code = $manager->generate2FACode($user);
                        EmailService::send2FACode($user->getEmail(), $code);
                    }
                    header('Location: index.php?page=2fa');
                    exit;
                } else {
                    $error = "Identifiants incorrects.";
                }
            }
        }
        require __DIR__ . '/../../templates/login.php';
    }

    public function register() {
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $captchaConfig = require __DIR__ . '/../../config/captchaConfig.php';
            $captchaService = new Captcha($captchaConfig['turnstile_secret']);
            $token = $_POST['cf-turnstile-response'] ?? '';
            
            if (!$captchaService->isValid($token, $_SERVER['REMOTE_ADDR'])) {
                $error = "Veuillez valider la sécurité (Captcha).";
            } else {
                $pwd = $_POST['password'];
                if (strlen($pwd) < 12 || !preg_match('/[A-Z]/', $pwd) || !preg_match('/[a-z]/', $pwd) || !preg_match('/[0-9]/', $pwd) || !preg_match('/[\W]/', $pwd)) {
                    $error = "Le mot de passe doit respecter la norme CNIL : 12 caractères minimum, avec majuscule, minuscule, chiffre et caractère spécial.";
                } else {
                    $user = new User([
                        'username'     => $_POST['username'],
                        'email'        => $_POST['email'],
                        'password'     => $_POST['password'],
                        'firstname'    => $_POST['firstname'] ?? '',
                        'lastname'     => $_POST['lastname'] ?? '',
                        'phone_number' => $_POST['phone_number'] ?? '',
                        'birth_year'   => $_POST['birth_year'] ?? 0,
                        'address'      => $_POST['address'] ?? '',
                        'role'         => 'user'
                    ]);

                    $manager = new UserManager();
                    try {
                        if ($manager->register($user)) {
                            header('Location: index.php?page=login');
                            exit;
                        } else {
                            $error = "Erreur lors de l'inscription.";
                        }
                    } catch (Exception $e) {
                        $error = "Ce nom d'utilisateur ou email est déjà pris.";
                    }
                }
            }
        }
        require __DIR__ . '/../../templates/register.php';
    }

    public function verify2FA() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['2fa_user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }

        $error = null;
        $manager = new UserManager();
        $user = $manager->getUserById($_SESSION['2fa_user_id']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = $_POST['code'];
            $isValid = false;

            if (isset($_SESSION['2fa_type']) && $_SESSION['2fa_type'] === 'totp') {
                $tfa = new TwoFactorAuthLight();
                if ($tfa->verifyCode($user->getTotpSecret(), $code)) {
                    $isValid = true;
                }
            } else {
                if ($manager->verify2FACode($_SESSION['2fa_user_id'], $code)) {
                    $isValid = true;
                }
            }

            if ($isValid) {
                UserSession::login($user->getIdUser(), $user->getUsername(), $user->getRole());
                unset($_SESSION['2fa_user_id'], $_SESSION['2fa_type']);
                
                $redirect = $_SESSION['redirect_after_login'] ?? 'index.php?page=home';
                unset($_SESSION['redirect_after_login']);
                header('Location: ' . $redirect);
                exit;
            } else {
                $error = "Code invalide ou expiré.";
            }
        }
        require __DIR__ . '/../../templates/2fa.php';
    }

    public function setup2FA() {
        if (!UserSession::isAuthenticated()) { header('Location: index.php?page=login'); exit; }
        require_once __DIR__ . '/../Service/TwoFactorAuthLight.php';
        require_once __DIR__ . '/../../phpqrcode/qrlib.php';

        $manager = new UserManager();
        $user = $manager->getUserById(UserSession::getUserId());
        $tfa = new TwoFactorAuthLight();

        if (!$user->getTotpSecret()) {
            $user->setTotpSecret($tfa->createSecret());
            $manager->updateProfile($user); 
        }

        $qrCodeUrl = $tfa->getQRCodeUrl($user->getEmail(), $user->getTotpSecret(), 'IMG2BRICKS');
        
        if (ob_get_level()) ob_end_clean(); 
        ob_start();
        QRcode::png($qrCodeUrl, false, 0, 4);
        $imgData = ob_get_contents();
        ob_end_clean();

        $qrImage = 'data:image/png;base64,' . base64_encode($imgData);
        
        require __DIR__ . '/../../templates/setup_2fa.php';
    }

    public function logout() {
        UserSession::logout();
        header('Location: index.php?page=home');
        exit;
    }

    public function profile() {
        if (!UserSession::isAuthenticated()) { header('Location: index.php?page=login'); exit; }
        $manager = new UserManager();
        $user = $manager->getUserById(UserSession::getUserId());
        $message = null;
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user->setFirstname($_POST['firstname']);
            $user->setLastname($_POST['lastname']);
            $user->setAddress($_POST['address']);
            $user->setPhoneNumber($_POST['phone']);
            $user->setEmail($_POST['email']);
            $user->setTotpEnabled(isset($_POST['totp_enabled']) ? 1 : 0);
            
            if (!empty($_POST['new_password'])) {
                $pwd = $_POST['new_password'];
                if (strlen($pwd) < 12 || !preg_match('/[A-Z]/', $pwd) || !preg_match('/[a-z]/', $pwd) || !preg_match('/[0-9]/', $pwd) || !preg_match('/[\W]/', $pwd)) {
                    $error = "Le nouveau mot de passe ne respecte pas la norme CNIL.";
                } else {
                    $user->setPassword(password_hash($pwd, PASSWORD_DEFAULT));
                    $emailBody = "<div style='font-family: Arial, sans-serif; color: #333;'><h2>Alerte de sécurité</h2><p>Bonjour,</p><p>Votre mot de passe a été modifié avec succès. Si vous n'êtes pas à l'origine de cette action, veuillez nous contacter immédiatement.</p></div>";
                    EmailService::sendEmail($user->getEmail(), "Modification de votre mot de passe", $emailBody);
                }
            }
            
            if (!$error && $manager->updateProfile($user)) {
                $message = "Profil mis à jour avec succès.";
            }
        }
        require __DIR__ . '/../../templates/profile.php';
    }
    public function exchange() {
        // check if user login
        if (!UserSession::isAuthenticated()) {
            header('Location: index.php?page=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['package'])) {
            $package_points = intval($_POST['package']);
            
            // define conversion rates
            $conversion_rates = [
                1000 => 1,
                10000 => 12,
                100000 => 150
            ];

            // If someone trie to send an invalid package, we stop them
            if (!array_key_exists($package_points, $conversion_rates)) {
                $_SESSION['error'] = "Pack d'échange invalide.";
                header('Location: index.php?page=coupons'); 
                exit;
            }

            $coins_to_add = $conversion_rates[$package_points];
            $user_id = $_SESSION['user']['id'] ?? $_SESSION['user']['user_id'];

            require_once __DIR__ . '/../../config/database.php';
            $db = Database::getInstance();
            
            // Get current points
            $stmt = $db->prepare("SELECT points FROM users WHERE user_id = :id");
            $stmt->execute(['id' => $user_id]);
            $current_points = $stmt->fetchColumn();

            // Check if the user has enough points
            if ($current_points >= $package_points) {
                // Subtract points and add coins
                $update_stmt = $db->prepare("UPDATE users SET points = points - :points, coins = coins + :coins WHERE user_id = :id");
                $update_stmt->execute([
                    'points' => $package_points,
                    'coins' => $coins_to_add,
                    'id' => $user_id
                ]);

                $_SESSION['success'] = "Échange réussi ! Vous avez reçu $coins_to_add Coin(s).";
            } else {
                $_SESSION['error'] = "Points insuffisants pour cet échange.";
            }
        }
        
        // Redirect back to coupons page with success or error message
        header('Location: index.php?page=coupons');
        exit;
    }

    public function forgotPassword() {
        $message = null;
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare("SELECT user_id, username FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
                $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires_at = ? WHERE user_id = ?");
                $stmt->execute([$token, $expires, $user['user_id']]);

                $resetLink = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/index.php?page=reset_password&token=" . $token;
                EmailService::sendResetLink($email, $resetLink);
                $message = "Un lien de réinitialisation a été envoyé à votre adresse email.";
            } else {
                $message = "Si cette adresse existe, un lien a été envoyé.";
            }
        }
        require __DIR__ . '/../../templates/forgot_password.php';
    }

    public function resetPassword() {
        $token = $_GET['token'] ?? '';
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE reset_token = ? AND reset_expires_at > NOW()");
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if (!$user) die("Jeton invalide ou expiré.");

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newPassword = $_POST['password'];
            if (strlen($newPassword) >= 12) {
                $hash = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires_at = NULL WHERE user_id = ?");
                $stmt->execute([$hash, $user['user_id']]);
                header('Location: index.php?page=login&reset=success');
                exit;
            } else {
                $error = "Le mot de passe doit contenir au moins 12 caractères.";
            }
        }
        require __DIR__ . '/../../templates/reset_password.php';
    }
    public function coupons() {
        if (!UserSession::isAuthenticated()) { 
            header('Location: index.php?page=login'); 
            exit; 
        }
        require __DIR__ . '/../../templates/coupons.php';
    }
}