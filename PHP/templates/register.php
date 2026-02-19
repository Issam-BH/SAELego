<?php ob_start(); ?>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">
                    <h2 class="mb-4 text-center text-primary"><?= Translator::get('register') ?></h2>
                    <p class="text-center text-muted mb-5">Rejoignez l'aventure des briques !</p>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger rounded-3 border-0 bg-danger-subtle text-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST" action="<?= LanguageService::getURLWithLanguage('register') ?>">

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small"><?= Translator::get('username') ?> *</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small"><?= Translator::get('email') ?> *</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small"><?= Translator::get('password') ?> *</label>
                            <input type="password" name="password" class="form-control" required
                                   minlength="12"
                                   pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{12,}"
                                   title="Must contain at least 12 characters...">
                            <div class="form-text text-muted">
                                <i class="bi bi-shield-lock"></i> 12 char min, Maj, Min, Chiffre, Special.
                            </div>
                        </div>

                        <div class="bg-light p-3 rounded-3 mb-4">
                            <h6 class="text-primary mb-3 small text-uppercase fw-bold"><?= Translator::get('personal_info') ?> (Optionnel)</h6>
                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <input type="text" name="firstname" class="form-control" placeholder="<?= Translator::get('first_name') ?>">
                                </div>
                                <div class="col-6">
                                    <input type="text" name="lastname" class="form-control" placeholder="<?= Translator::get('last_name') ?>">
                                </div>
                            </div>
                            <div class="mb-2">
                                <textarea name="address" class="form-control" rows="2" placeholder="<?= Translator::get('full_address') ?>"></textarea>
                            </div>
                        </div>

                        <div class="mb-4 d-flex justify-content-center">
                            <div class="cf-turnstile" data-sitekey="0x4AAAAAAB_ZMzeWVzF1-98z"></div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-lg"><?= Translator::get('register') ?></button>
                    </form>

                    <div class="text-center mt-4">
                        <a href="<?= LanguageService::getURLWithLanguage('login') ?>" class="text-decoration-none fw-bold text-primary">
                            <?= Translator::get('already_have_account') ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>