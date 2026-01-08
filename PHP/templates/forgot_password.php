<?php


require_once __DIR__ . '/../src/Service/EmailService.php';
$message = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = Translator::get('invalid_email');
    } else {
        try {
            $stmt = $pdo->prepare("SELECT user_id, nickname FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $token = bin2hex(random_bytes(32));

                $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

                $updateSql = "UPDATE users SET reset_token = ?, reset_expires_at = ? WHERE user_id = ?";
                $updateStmt = $pdo->prepare($updateSql);
                $updateStmt->execute([$token, $expiry, $user['user_id']]);

                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
                $domain = $_SERVER['HTTP_HOST'];
                $basePath = dirname($_SERVER['REQUEST_URI']);
                $link = BASE_URL . "/index.php?page=reset_password&token=" . $token;

                $subject = "Reset your Password - SAELego";
                $body = "
                    <h3>Hello " . htmlspecialchars($user['nickname']) . ",</h3>
                    <p>We received a request to reset your password.</p>
                    <p>Click the link below to set a new password:</p>
                    <p><a href='" . $link . "' style='padding: 10px 15px; background-color: #0d6efd; color: white; text-decoration: none; border-radius: 5px;'>Reset Password</a></p>
                    <p><small>Or copy this link: " . $link . "</small></p>
                    <p>This link is valid for <strong>1 hour</strong>.</p>
                ";

                if (EmailService::sendEmail($email, $subject, $body)) {
                    $message = Translator::get('reset_link_sent');
                } else {
                    $error = Translator::get('error_sending_email');
                }
            } else {

                $message = Translator::get('reset_link_sent');
            }
        } catch (Exception $e) {
            $error = "An error occurred. Please try again.";
        }
    }
}

ob_start(); ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <h2 class="mb-3"><?= Translator::get('forgot_your_password') ?></h2>
        <p class="text-muted"><?= Translator::get('enter_email_reset') ?></p>

        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= LanguageService::getURLWithLanguage('forgot_password') ?>">
            <div class="mb-3">
                <label class="form-label"><?= Translator::get('email') ?></label>
                <input type="email" name="email" class="form-control" required placeholder="name@example.com">
            </div>
            <button type="submit" class="btn btn-primary w-100"><?= Translator::get('send_link') ?></button>
        </form>

        <div class="text-center mt-3">
            <a href="<?= LanguageService::getURLWithLanguage('login') ?>"><?= Translator::get('back_to_login') ?></a>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>