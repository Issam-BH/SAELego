<?php ob_start(); ?>
    <h2 class="mb-4">Finalize your order</h2>

    <div class="row">
        <div class="col-md-4 order-md-last mb-4">
            <h4 class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-primary">Your Mosaic</span>
            </h4>
            <ul class="list-group mb-3">
                <li class="list-group-item d-flex justify-content-between lh-sm">
                    <div>
                        <h6 class="my-0">LEGO® Mosaic</h6>
                        <small class="text-muted">Size: <?= htmlspecialchars($size) ?>x<?= htmlspecialchars($size) ?></small>
                    </div>
                    <span class="text-muted"><?= htmlspecialchars($price) ?> €</span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Total (EUR)</span>
                    <strong><?= htmlspecialchars($price) ?> €</strong>
                </li>
            </ul>

            <div class="card">
                <img src="image.php?id=<?= $uploadId ?>" class="card-img-top" style="filter: <?= htmlspecialchars($filter) ?>;">
                <div class="card-body text-center text-muted small">Preview of the result</div>
            </div>
        </div>

        <div class="col-md-8">
            <form action="index.php?page=process_order" method="POST">
                <input type="hidden" name="upload_id" value="<?= $uploadId ?>">
                <input type="hidden" name="total_price" value="<?= $price ?>">
                <input type="hidden" name="filter_css" value="<?= htmlspecialchars($filter) ?>">
                <input type="hidden" name="size_option" value="<?= htmlspecialchars($size) ?>">

                <h4 class="mb-3">Shipping Address</h4>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Address</label>
                        <input type="text" class="form-control" name="address" value="<?= htmlspecialchars($user['address'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">City</label>
                        <input type="text" class="form-control" name="city" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Zip Code</label>
                        <input type="text" class="form-control" name="zip" required>
                    </div>
                </div>

                <hr class="my-4">

                <h4 class="mb-3">Payment (Simulation)</h4>
                <div class="alert alert-warning">
                    Payment mode simulated for project purposes (no real payment).
                </div>

                <div class="row gy-3">
                    <div class="col-md-6">
                        <label class="form-label">Name on card</label>
                        <input type="text" class="form-control" value="Mr. Tester" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Card number</label>
                        <input type="text" class="form-control" value="4242 4242 4242 4242" readonly style="background-color: #e9ecef;">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Expiration</label>
                        <input type="text" class="form-control" value="12/34" readonly style="background-color: #e9ecef;">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">CVV</label>
                        <input type="text" class="form-control" value="123" readonly style="background-color: #e9ecef;">
                    </div>
                </div>

                <hr class="my-4">

                <button class="w-100 btn btn-primary btn-lg" type="submit">Confirm and Pay</button>
            </form>
        </div>
    </div>
<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>