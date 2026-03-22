<?php ob_start(); ?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-4 p-5">
                <h2 class="mb-4 fw-bold text-center">Finaliser votre commande</h2>
                
                <form action="index.php?page=order_process" method="POST" id="order-form">
                    <input type="hidden" name="upload_id" value="<?= htmlspecialchars($uploadId) ?>">
                    <input type="hidden" name="filter_css" value="<?= htmlspecialchars($filter) ?>">
                    <input type="hidden" name="size_option" value="<?= htmlspecialchars($size) ?>">
                    <input type="hidden" name="total_price" id="base_price" value="<?= htmlspecialchars($price) ?>">
                    <input type="hidden" name="brick_data" value='<?= htmlspecialchars($brickData, ENT_QUOTES, 'UTF-8') ?>'>
                    
                    <h4 class="mb-3 text-primary"><i class="bi bi-geo-alt-fill me-2"></i>Adresse de livraison</h4>
                    <div class="mb-3">
                        <input type="text" class="form-control form-control-lg bg-light" name="address" placeholder="Adresse complète" required>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <input type="text" class="form-control form-control-lg bg-light" name="city" placeholder="Ville" required>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-lg bg-light" name="zip" placeholder="Code Postal" required>
                        </div>
                    </div>

                    <h4 class="mb-3 text-primary"><i class="bi bi-tag-fill me-2"></i>Code Promo (Fidélité)</h4>
                    <div class="input-group mb-2">
                        <input type="text" class="form-control form-control-lg bg-light" name="coupon_code" id="coupon_code" placeholder="Ex: LEGO10 ou LEGO20">
                        <button class="btn btn-outline-secondary px-4 fw-bold" type="button" onclick="applyCoupon()">Appliquer</button>
                    </div>
                    <div id="coupon-message" class="small fw-bold mb-4 ms-1"></div>

                    <h4 class="mb-3 text-primary"><i class="bi bi-credit-card-fill me-2"></i>Paiement</h4>
                    <div class="bg-light p-3 rounded-3 mb-2 border">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" value="card" id="pay_card" checked>
                            <label class="form-check-label fw-bold" for="pay_card">Carte Bancaire</label>
                        </div>
                    </div>
                    <div class="bg-light p-3 rounded-3 mb-4 border border-primary">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" value="paypal" id="pay_paypal">
                            <label class="form-check-label fw-bold text-primary" for="pay_paypal">
                                <i class="bi bi-paypal me-1"></i> PayPal Sandbox (Simulation)
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success btn-lg w-100 fw-bold shadow py-3" style="font-size: 1.2rem;">
                        <i class="bi bi-lock-fill me-2"></i>Payer <span id="display_price"><?= number_format($price, 2) ?></span> €
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function applyCoupon() {
        const code = document.getElementById('coupon_code').value.trim().toUpperCase();
        const basePrice = parseFloat(document.getElementById('base_price').value);
        let finalPrice = basePrice;
        let msg = document.getElementById('coupon-message');
        
        if (code === 'LEGO10') {
            finalPrice = basePrice * 0.90;
            msg.innerHTML = '<span class="text-success"><i class="bi bi-check-circle-fill"></i> Réduction de 10% appliquée !</span>';
        } else if (code === 'LEGO20') {
            finalPrice = basePrice * 0.80;
            msg.innerHTML = '<span class="text-success"><i class="bi bi-check-circle-fill"></i> Réduction de 20% appliquée !</span>';
        } else {
            msg.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle-fill"></i> Code promo invalide ou expiré.</span>';
        }
        document.getElementById('display_price').innerText = finalPrice.toFixed(2);
    }
</script>
<?php $content = ob_get_clean(); require 'layout.php'; ?>