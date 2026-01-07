<?php ob_start(); ?>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="mb-3">Forgot your password</h2>
            <p class="text-muted">Enter your email address to receive a reset link.</p>

            <?php if (isset($message)): ?>
                <div class="alert alert-success"><?= $message ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" action="index.php?page=forgot_password">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Send the link</button>
            </form>

            <div class="text-center mt-3">
                <a href="index.php?page=login">Back to login</a>
            </div>
        </div>
    </div>
<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>