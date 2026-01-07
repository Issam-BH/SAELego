<?php ob_start(); ?>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="mb-3">Login</h2>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" action="index.php?page=login">
                <div class="mb-3">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="mb-3 d-flex justify-content-center">
                    <div class="cf-turnstile" data-sitekey="0x4AAAAAAB_ZMzeWVzF1-98z"></div>
                </div>

                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>

            <div class="mb-3 text-end">
                <a href="index.php?page=forgot_password" class="small text-decoration-none">Forgot password?</a>
            </div>

            <hr>
            <div class="text-center">
                <a href="index.php?page=register">Create an account</a>
            </div>
        </div>
    </div>
<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>