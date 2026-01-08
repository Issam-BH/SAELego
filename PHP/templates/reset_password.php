<?php
$token = $_GET['token'] ?? '';
$pdo = Database::getInstance();
$stmt = $pdo->prepare("SELECT user_id FROM users WHERE reset_token = ? AND reset_expires_at > NOW()");
$stmt->execute([$token]);
$user = $stmt->fetch();

$error = null;
$success = null;
$showForm = false;

if (!$user) {
    $error = Translator::get('invalid_token');
} else {
    $showForm = true;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $newPassword = $_POST['password'] ?? '';
        $confirmPassword = $_POST['password_confirm'] ?? '';

        if (strlen($newPassword) < 12) {
            $error = Translator::get('password_too_short');
        } elseif ($newPassword !== $confirmPassword) {
            $error = Translator::get('password_mismatch');
        } else {
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires_at = NULL WHERE user_id = ?");
            
            if ($stmt->execute([$hash, $user['user_id']])) {
                $success = Translator::get('password_reset_success');
                $showForm = false;
            } else {
                $error = Translator::get('error_updating_password');
            }
        }
    }
}

ob_start(); ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <h2 class="mb-3"><?= Translator::get('reset_password') ?></h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success) ?> <br>
                <a href="<?= LanguageService::getURLWithLanguage('login') ?>" class="alert-link"><?= Translator::get('go_back_login') ?></a>.
            </div>
        <?php endif; ?>

        <?php if ($showForm): ?>
            <form method="POST" action="<?= LanguageService::getURLWithLanguage('reset_password', ['token' => $token]) ?>">
                <div class="mb-3">
                    <label class="form-label"><?= Translator::get('new_password') ?></label>
                    <input type="password" name="password" class="form-control" required minlength="12">
                </div>
                <div class="mb-3">
                    <label class="form-label"><?= Translator::get('confirm_password') ?></label>
                    <input type="password" name="password_confirm" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100"><?= Translator::get('reset_password') ?></button>
            </form>
        <?php endif; ?>
        
        <?php if (!$showForm && !$success): ?>
             <div class="text-center mt-3">
                <a href="<?= LanguageService::getURLWithLanguage('forgot_password') ?>"><?= Translator::get('request_new_link') ?></a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>
