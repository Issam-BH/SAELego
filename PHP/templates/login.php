<?php ob_start(); ?>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

    <div class="row justify-content-center align-items-center" style="min-height: 60vh;">
        <div class="col-md-5">
            <div class="card p-4">
                <div class="card-body">
                    <h2 class="text-center mb-4 text-primary"><?= Translator::get('login') ?></h2>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger rounded-3 border-0 bg-danger-subtle text-danger mb-4">
                            <i class="bi bi-exclamation-circle-fill me-2"></i> <?= $error ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= LanguageService::getURLWithLanguage('login') ?>">
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small text-uppercase"><?= Translator::get('username') ?></label>
                            <input type="text" name="username" class="form-control form-control-lg" required placeholder="Votre pseudo">
                        </div>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between">
                                <label class="form-label fw-bold text-muted small text-uppercase"><?= Translator::get('password') ?></label>
                                <a href="<?= LanguageService::getURLWithLanguage('forgot_password') ?>" class="small text-decoration-none text-primary">
                                    <?= Translator::get('forgot_password_q') ?>
                                </a>
                            </div>
                            <input type="password" name="password" class="form-control form-control-lg" required placeholder="••••••••">
                        </div>

                        <div class="mb-4 d-flex justify-content-center">
                            <div class="cf-turnstile" data-sitekey="0x4AAAAAAB_ZMzeWVzF1-98z"></div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-lg py-3">
                            <?= Translator::get('login') ?> <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </form>

                    <hr class="my-4 text-muted">
                    
                    <div class="text-center">
                        <p class="text-muted mb-2">Pas encore de compte ?</p>
                        <a href="<?= LanguageService::getURLWithLanguage('register') ?>" class="btn btn-outline-primary w-100 rounded-pill">
                            <?= Translator::get('create_account') ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>