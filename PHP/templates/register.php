<?php ob_start(); ?>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h2 class="mb-4 text-center"><?= Translator::get('register') ?></h2>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST" action="<?= LanguageService::getURLWithLanguage('register') ?>">

                        <h5 class="text-primary mb-3"><?= Translator::get('credentials') ?></h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label"><?= Translator::get('username') ?> *</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><?= Translator::get('email') ?> *</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><?= Translator::get('password') ?> *</label>
                            <input type="password" name="password" class="form-control" required
                                   minlength="12"
                                   pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{12,}"
                                   title="Must contain at least 12 characters, one uppercase, one lowercase, one number and one special character.">
                            <div class="form-text text-muted">
                                <small><?= Translator::get('security_compliance') ?></small>
                            </div>
                        </div>

                        <hr>

                        <h5 class="text-primary mb-3"><?= Translator::get('personal_info') ?></h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label"><?= Translator::get('first_name') ?></label>
                                <input type="text" name="firstname" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><?= Translator::get('last_name') ?></label>
                                <input type="text" name="lastname" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label"><?= Translator::get('phone_number') ?></label>
                                <input type="text" name="phone_number" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><?= Translator::get('birth_year') ?></label>
                                <input type="number" name="birth_year" class="form-control" placeholder="ex: 2000" min="1900" max="<?= date('Y') ?>">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label"><?= Translator::get('full_address') ?></label>
                            <textarea name="address" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="mb-4 d-flex justify-content-center">
                            <div class="cf-turnstile" data-sitekey="0x4AAAAAAB_ZMzeWVzF1-98z"></div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 btn-lg"><?= Translator::get('register') ?></button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="<?= LanguageService::getURLWithLanguage('login') ?>"><?= Translator::get('already_have_account') ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>