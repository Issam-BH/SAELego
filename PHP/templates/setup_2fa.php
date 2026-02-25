<?php ob_start(); ?>
<div class="row justify-content-center">
    <div class="col-md-6 text-center">
        <h3 class="mb-3">Configuration Google Authenticator</h3>
        <p>Scannez ce QR Code avec votre application d'authentification.</p>
        
        <div class="mb-4">
            <img src="<?= $qrImage ?>" alt="QR Code 2FA" class="img-thumbnail">
        </div>
        
        <p class="small text-muted mb-4">
            Si vous ne pouvez pas scanner le code, vous pouvez entrer cette clé manuellement :<br>
            <strong><?= htmlspecialchars($user->getTotpSecret()) ?></strong>
        </p>

        <a href="index.php?page=profile" class="btn btn-primary">Retour au profil</a>
    </div>
</div>
<?php $content = ob_get_clean(); require 'layout.php'; ?>