<?php ob_start(); ?>
    <div class="row">
        <div class="col-md-8 mx-auto">
            <h2>My Account</h2>
            <?php if (isset($message)): ?>
                <div class="alert alert-success"><?= $message ?></div>
            <?php endif; ?>

            <form method="POST" action="index.php?page=profile">
                <div class="row mb-3">
                    <div class="col">
                        <label>First Name</label>
                        <input type="text" name="firstname" class="form-control" value="<?= htmlspecialchars($user->getFirstname() ?? '') ?>">
                    </div>
                    <div class="col">
                        <label>Last Name</label>
                        <input type="text" name="lastname" class="form-control" value="<?= htmlspecialchars($user->getLastname() ?? '') ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user->getEmail() ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label>Full Address</label>
                    <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($user->getAddress() ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label>Phone Number</label>
                    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user->getPhoneNumber() ?? '') ?>">
                </div>

                <button type="submit" class="btn btn-primary">Update my info</button>
            </form>
        </div>
    </div>
<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>