<?php
require_once __DIR__ . '/../config/database.php';

$token = $_GET['token'] ?? null;
$error = null;
$success = null;
$showForm = false;

if (!$token) {
    $error = "Token manquant.";
} else {
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE reset_token = ? AND reset_expires_at > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if (!$user) {
        $error = "Ce lien de réinitialisation est invalide ou a expiré.";
    } else {
        $showForm = true;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pass1 = $_POST['password'];
            $pass2 = $_POST['password_confirm'];

            if ($pass1 !== $pass2) {
                $error = "Les mots de passe ne correspondent pas.";
            } elseif (strlen($pass1) < 8) {
                $error = "Le mot de passe doit faire au moins 8 caractères.";
            } else {
                $hashedPassword = password_hash($pass1, PASSWORD_DEFAULT);
                
                $updateSql = "UPDATE users SET password = ?, reset_token = NULL, reset_expires_at = NULL WHERE user_id = ?";
                $updateStmt = $pdo->prepare($updateSql);
                
                if ($updateStmt->execute([$hashedPassword, $user['user_id']])) {
                    $success = "Votre mot de passe a été modifié avec succès.";
                    $showForm = false; 
                } else {
                    $error = "Erreur lors de la mise à jour.";
                }
            }
        }
    }
}

ob_start(); ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <h2 class="mb-3">Nouveau mot de passe</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success) ?> <br>
                <a href="index.php?page=login" class="alert-link">Connectez-vous ici</a>.
            </div>
        <?php endif; ?>

        <?php if ($showForm): ?>
            <form method="POST" action="index.php?page=reset_password&token=<?= htmlspecialchars($token) ?>">
                <div class="mb-3">
                    <label class="form-label">Nouveau mot de passe</label>
                    <input type="password" name="password" class="form-control" required minlength="8">
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirm" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Changer le mot de passe</button>
            </form>
        <?php endif; ?>
        
        <?php if (!$showForm && !$success): ?>
             <div class="text-center mt-3">
                <a href="index.php?page=forgot_password">Demander un nouveau lien</a>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>