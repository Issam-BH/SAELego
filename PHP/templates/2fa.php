<?php ob_start(); ?>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h3 class="mb-3">Two-Factor Authentication</h3>
            <div class="alert alert-info">A code has been sent to your email</div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" action="index.php?page=2fa">
                <div class="mb-3">
                    <label>Security code (6 numbers)</label>
                    <input type="text" name="code" class="form-control" maxlength="6" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Verify</button>
            </form>
        </div>
    </div>
<?php $content = ob_get_clean(); require 'layout.php'; ?>