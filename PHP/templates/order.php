<?php 
ob_start(); 

$session_id = $_SESSION['user']['id'] ?? $_SESSION['user']['user_id'] ?? 0;
$user_coins = 0;

if ($session_id > 0) {
    require_once __DIR__ . '/../config/database.php'; 
    $db = Database::getInstance();
    $stmt_coins = $db->prepare("SELECT coins FROM users WHERE user_id = :id");
    $stmt_coins->execute(['id' => $session_id]);
    $user_coins = $stmt_coins->fetchColumn() ?: 0;
}
?>
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-4 p-5">
                <h2 class="mb-4 fw-bold text-center">Finaliser votre commande</h2>
                
                <form action="index.php?page=order_process" method="POST" id="order-form">
                    <input type="hidden" name="upload_id" value="<?= htmlspecialchars($uploadId ?? '') ?>">
                    <input type="hidden" name="filter_css" value="<?= htmlspecialchars($filter ?? '') ?>">
                    <input type="hidden" name="size_option" value="<?= htmlspecialchars($size ?? '') ?>">
                    <input type="hidden" name="total_price" id="base_price" value="<?= htmlspecialchars($price ?? 0) ?>">
                    <input type="hidden" name="brick_data" value='<?= htmlspecialchars($brickData ?? '', ENT_QUOTES, 'UTF-8') ?>'>
                    
                    <input type="hidden" name="used_coins" id="used_coins_hidden" value="0">
                    
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

                    <h4 class="mb-3 text-success">Utiliser vos Coins</h4>
                    <div class="d-flex align-items-center mb-4 p-3 bg-light rounded-3 border">
                        <div class="me-auto">
                            <span class="fw-bold text-muted">Solde disponible :</span> 
                            <span class="badge bg-success fs-6 ms-2"><?= $user_coins ?> Coins</span>
                            <div class="small text-muted mt-1">1 Coin = 1 € de réduction</div>
                        </div>
                        
                        <div class="input-group" style="width: 140px;">
                            <button class="btn btn-outline-success fw-bold" type="button" onclick="adjustCoins(-1)">-</button>
                            <input type="text" class="form-control text-center fw-bold bg-white text-success" id="coin_input" value="0" readonly>
                            <button class="btn btn-outline-success fw-bold" type="button" onclick="adjustCoins(1)">+</button>
                        </div>
                    </div>

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

                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow py-3" style="font-size: 1.2rem;">
                        <i class="bi bi-lock-fill me-2"></i>Payer <span id="display_price"><?= number_format($price ?? 0, 2) ?></span> €
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    const maxUserCoins = <?= $user_coins ?>; 
    const basePrice = parseFloat(document.getElementById('base_price').value);
    
    let discountMultiplier = 1; 
    let usedCoins = 0;          

    function applyCoupon() {
        const code = document.getElementById('coupon_code').value.trim().toUpperCase();
        let msg = document.getElementById('coupon-message');
        
        if (code === 'LEGO10') {
            discountMultiplier = 0.90;
            msg.innerHTML = '<span class="text-success"><i class="bi bi-check-circle-fill"></i> Réduction de 10% appliquée !</span>';
        } else if (code === 'LEGO20') {
            discountMultiplier = 0.80;
            msg.innerHTML = '<span class="text-success"><i class="bi bi-check-circle-fill"></i> Réduction VIP de 20% appliquée !</span>';
        } else {
            discountMultiplier = 1; 
            msg.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle-fill"></i> Code promo invalide ou expiré.</span>';
        }
        
        updateTotals();
    }

    function adjustCoins(change) {
        usedCoins += change;
        updateTotals();
    }

    function updateTotals() {
        let discountedPrice = basePrice * discountMultiplier;
        
        let maxUsableCoins = Math.min(maxUserCoins, Math.floor(discountedPrice));
        
        if (usedCoins > maxUsableCoins) {
            usedCoins = maxUsableCoins;
        }
        if (usedCoins < 0) {
            usedCoins = 0;
        }
        
        document.getElementById('coin_input').value = usedCoins;
        document.getElementById('used_coins_hidden').value = usedCoins;
        
        let finalPrice = discountedPrice - usedCoins;
        
        document.getElementById('display_price').innerText = finalPrice.toFixed(2);
    }
</script>

<?php 
$content = ob_get_clean(); 
require 'layout.php'; 
?>