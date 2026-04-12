<?php ob_start(); ?>

<style>
    .text-gradient-primary {
        background: linear-gradient(90deg, #F06292 0%, #7B1FA2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .section-title {
        color: #4a4a4a;
        font-weight: bold;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .balance-badge {
        font-size: 1.15rem;
        padding: 0.6rem 1.2rem;
        border-radius: 50rem;
        background-color: #ffffff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        color: #333;
    }

    .balance-points {
        border: 1px solid #ffc107;
    }

    .balance-coins {
        border: 1px solid #198754;
    }

    .exchange-card {
        border: 1px solid #e9ecef;
        border-radius: 1rem;
        background-color: #ffffff;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .exchange-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08);
    }

    .exchange-icon {
        font-size: 3rem;
        margin-bottom: 0.5rem;
    }

    .exchange-price {
        font-size: 1.25rem;
        font-weight: bold;
        margin-bottom: 1rem;
    }

    /* Buttons for Exchange */
    .btn-exchange {
        width: 100%;
        font-weight: bold;
        border-radius: 50rem;
        color: #ffffff;
    }
    
    .btn-exchange-basic { background-color: #f39c12; }
    .btn-exchange-popular { background-color: #0d6efd; }
    .btn-exchange-legendary { background-color: #198754; }

    .btn-exchange:hover {
        opacity: 0.9;
        color: #ffffff;
    }

    .coupon-card {
        border: none;
        border-radius: 1rem;
        position: relative;
        overflow: hidden;
        transition: transform 0.3s ease;
    }

    .coupon-card:hover {
        transform: scale(1.02);
    }

    /* Themes for coupons */
    .coupon-theme-standard {
        background: linear-gradient(135deg, #FF9A9E 0%, #FECFEF 100%);
    }

    .coupon-theme-vip {
        background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%);
    }

    /* Code display box inside coupon */
    .coupon-code-box {
        background-color: #ffffff;
        border-radius: 0.5rem;
        padding: 0.75rem 1.5rem;
        display: inline-block;
        border: 2px dashed #ffffff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .coupon-code-text {
        font-size: 1.75rem;
        font-weight: bold;
        letter-spacing: 3px;
        user-select: all; 
    }

    .text-standard { color: #FF9A9E; }
    .text-vip { color: #a18cd1; }

    /* Decorative holes for the ticket effect */
    .coupon-hole {
        position: absolute;
        top: 50%;
        width: 30px;
        height: 30px;
        background-color: #f8f9fa; 
        border-radius: 50%;
        transform: translateY(-50%);
    }
    
    .hole-left { left: -15px; }
    .hole-right { right: -15px; }
</style>

<div class="container mt-5 mb-5">
    
    <div class="text-center mb-5">
        <h1 class="fw-bold text-gradient-primary">
            Banque de Fidélité
        </h1>
        <p class="text-muted lead">Échangez vos points contre des Coins (1 Coin = 1€) ou utilisez vos codes promo.</p>

        <?php
        $current_points = 0;
        $current_coins = 0;

        if (class_exists('UserSession') && UserSession::isAuthenticated()) {
            require_once __DIR__ . '/../config/database.php';
            $db_page = Database::getInstance();
            $session_id = $_SESSION['user']['id'] ?? $_SESSION['user']['user_id'] ?? 0;
            
            if ($session_id > 0) {
                $stmt_user = $db_page->prepare("SELECT points, coins FROM users WHERE user_id = :id");
                $stmt_user->execute(['id' => $session_id]);
                $user_data = $stmt_user->fetch(PDO::FETCH_ASSOC);
                
                if ($user_data) {
                    $current_points = $user_data['points'] ?? 0;
                    $current_coins = $user_data['coins'] ?? 0; 
                }
            }
        }
        ?>
        
        <?php if (class_exists('UserSession') && UserSession::isAuthenticated()): ?>
            <div class="d-inline-flex gap-3 mt-3">
                <span class="balance-badge balance-points">
                    🪙 <?php echo $current_points; ?> Points
                </span>
                <span class="balance-badge balance-coins">
                    💰 <?php echo $current_coins; ?> Coins
                </span>
            </div>
        <?php else: ?>
            <div class="alert alert-warning mt-3 d-inline-block rounded-pill">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> Connectez-vous pour voir votre solde et échanger vos points !
            </div>
        <?php endif; ?>
    </div>

    <h3 class="section-title">Bureau de Change</h3>
    <div class="row justify-content-center g-4 mb-5 pb-5 border-bottom">
        
        <div class="col-md-3">
            <div class="exchange-card h-100 text-center p-4">
                <div class="exchange-icon">🪙</div>
                <h4 class="fw-bold">1 Coin</h4>
                <p class="text-muted small">Pack Basique</p>
                <hr class="text-muted">
                <div class="exchange-price text-warning">1 000 pts</div>
                
                <form action="index.php?page=exchange" method="POST">
                    <input type="hidden" name="package" value="1000">
                    <button type="submit" class="btn btn-exchange btn-exchange-basic" <?= $current_points < 1000 ? 'disabled' : '' ?>>
                        Échanger
                    </button>
                </form>
            </div>
        </div>

        <div class="col-md-3">
            <div class="exchange-card h-100 text-center p-4">
                <div class="exchange-icon">💰</div>
                <h4 class="fw-bold text-primary">12 Coins</h4>
                <p class="text-muted small">Pack Populaire</p>
                <hr class="text-muted">
                <div class="exchange-price text-primary">10 000 pts</div>
                
                <form action="index.php?page=exchange" method="POST">
                    <input type="hidden" name="package" value="10000">
                    <button type="submit" class="btn btn-exchange btn-exchange-popular" <?= $current_points < 10000 ? 'disabled' : '' ?>>
                        Échanger
                    </button>
                </form>
            </div>
        </div>

        <div class="col-md-3">
            <div class="exchange-card h-100 text-center p-4">
                <div class="exchange-icon">💎</div>
                <h4 class="fw-bold text-success">150 Coins</h4>
                <p class="text-muted small">Pack Légendaire</p>
                <hr class="text-muted">
                <div class="exchange-price text-success">100 000 pts</div>
                
                <form action="index.php?page=exchange" method="POST">
                    <input type="hidden" name="package" value="100000">
                    <button type="submit" class="btn btn-exchange btn-exchange-legendary" <?= $current_points < 100000 ? 'disabled' : '' ?>>
                        Échanger
                    </button>
                </form>
            </div>
        </div>
    </div>

    <h3 class="section-title mt-5">Mes Tickets de Fidélité</h3>
    <div class="row justify-content-center g-4">
        
        <div class="col-md-5">
            <div class="card shadow-lg coupon-card coupon-theme-standard h-100">
                <div class="coupon-hole hole-left"></div>
                <div class="coupon-hole hole-right"></div>
                
                <div class="card-body p-5 text-center">
                    <h3 class="fw-bold text-white mb-2">Réduction de 10%</h3>
                    <p class="text-white mb-4" style="opacity: 0.9;">Valable sur toutes les dimensions de mosaïque.</p>
                    
                    <div class="coupon-code-box">
                        <span class="coupon-code-text text-standard">LEGO10</span>
                    </div>
                    <p class="small text-white mt-3 mb-0">
                        <i class="bi bi-info-circle me-1"></i>Copiez ce code dans le panier
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow-lg coupon-card coupon-theme-vip h-100">
                <div class="coupon-hole hole-left"></div>
                <div class="coupon-hole hole-right"></div>
                
                <div class="card-body p-5 text-center">
                    <h3 class="fw-bold text-white mb-2">Réduction VIP 20%</h3>
                    <p class="text-white mb-4" style="opacity: 0.9;">Offre spéciale pour nos meilleurs créateurs.</p>
                    
                    <div class="coupon-code-box">
                        <span class="coupon-code-text text-vip">LEGO20</span>
                    </div>
                    <p class="small text-white mt-3 mb-0">
                        <i class="bi bi-info-circle me-1"></i>Copiez ce code dans le panier
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5 text-center">
        <a href="index.php?page=home" class="btn btn-outline-secondary px-5 py-3 fw-bold rounded-pill shadow-sm">
            <i class="bi bi-arrow-left me-2"></i>Retourner créer une mosaïque
        </a>
    </div>

</div>

<?php 
$content = ob_get_clean(); 
require 'layout.php'; 
?>