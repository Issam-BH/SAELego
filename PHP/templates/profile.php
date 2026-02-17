<?php ob_start(); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <div class="d-flex align-items-center mb-4 border-bottom pb-3">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div>
                        <h2 class="mb-0 h4">Mon Profil</h2>
                        <span class="text-muted small">Gérez vos informations personnelles</span>
                    </div>
                </div>

                <?php if (isset($message)): ?>
                    <div class="alert alert-success rounded-3 border-0 bg-success-subtle text-success mb-4"><?= $message ?></div>
                <?php endif; ?>

                <form method="POST" action="index.php?page=profile">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Prénom</label>
                            <input type="text" name="firstname" class="form-control" value="<?= htmlspecialchars($user->getFirstname() ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Nom</label>
                            <input type="text" name="lastname" class="form-control" value="<?= htmlspecialchars($user->getLastname() ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user->getEmail() ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Adresse</label>
                        <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($user->getAddress() ?? '') ?>">
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">Téléphone</label>
                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user->getPhoneNumber() ?? '') ?>">
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4">Enregistrer les modifications</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>