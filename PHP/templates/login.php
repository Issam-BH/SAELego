<?php ob_start(); ?>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="mb-3"><?= Translator::get('login') ?></h2>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" action="<?= LanguageService::getURLWithLanguage('login') ?>">
                <div class="mb-3">
                    <label><?= Translator::get('username') ?></label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label><?= Translator::get('password') ?></label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="mb-3 d-flex justify-content-center">
                    <div class="cf-turnstile" data-sitekey="0x4AAAAAAB_ZMzeWVzF1-98z"></div>
                </div>

                <button type="submit" class="btn btn-primary w-100"><?= Translator::get('login') ?></button>
            </form>

            <div class="mb-3 text-end">
                <a href="<?= LanguageService::getURLWithLanguage('forgot_password') ?>" class="small text-decoration-none"><?= Translator::get('forgot_password_q') ?></a>
            </div>

            <hr>
            <div class="text-center">
                <a href="<?= LanguageService::getURLWithLanguage('register') ?>"><?= Translator::get('create_account') ?></a>
            </div>
        </div>
    </div>
<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>