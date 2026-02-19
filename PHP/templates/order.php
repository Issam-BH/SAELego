<?php ob_start(); ?>
<div class="container">
    <h2 class="mb-4 text-center">Finaliser votre commande</h2>
    
    <div class="row g-5">
        <div class="col-lg-7">
            <div class="card p-4">
                <h4 class="mb-3 text-primary">Livraison</h4>
                <form action="index.php?page=order_process" method="POST">
                    <input type="hidden" name="upload_id" value="<?= $uploadId ?>">
                    <input type="hidden" name="total_price" value="<?= $price ?>">
                    <input type="hidden" name="size_option" value="<?= $size ?>">
                    <input type="hidden" name="filter_css" value="<?= htmlspecialchars($filter) ?>">
                    <input type="hidden" name="brick_data" value="<?= htmlspecialchars($brickData) ?>">

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Adresse complète</label>
                        <input type="text" name="address" class="form-control" required placeholder="ex: 12 Rue des Fleurs" value="<?= htmlspecialchars($user['address'] ?? '') ?>">
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Ville</label>
                            <input type="text" name="city" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Code Postal</label>
                            <input type="text" name="zip" class="form-control" required>
                        </div>
                    </div>

                    <h4 class="mt-5 mb-3 text-primary">Paiement</h4>
                    <div class="alert alert-info border-0 bg-info-subtle text-info-emphasis rounded-3">
                        <i class="bi bi-credit-card me-2"></i> Simulation de paiement sécurisé (Stripe/PayPal)
                    </div>

                    <button type="submit" class="btn btn-success w-100 btn-lg mt-4 py-3 shadow-sm">
                        Payer <?= number_format($price, 2) ?> €
                    </button>
                </form>
            </div>
        </div>
        
        <div class="col-lg-5">
            <div class="card bg-light border-0">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-4">Récapitulatif</h5>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Kit Mosaïque (<?= $size ?>x<?= $size ?>)</span>
                        <span class="fw-bold"><?= number_format($price, 2) ?> €</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 text-muted">
                        <span>Livraison</span>
                        <span>Gratuite</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="h5 mb-0">Total</span>
                        <span class="h3 mb-0 text-primary fw-bold"><?= number_format($price, 2) ?> €</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>