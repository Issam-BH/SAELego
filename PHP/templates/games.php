<?php ob_start(); ?>

<div class="container mt-5 mb-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold" style="font-family: 'Fredoka', sans-serif; background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            Espace Jeux Lego
        </h1>
        <p class="text-muted lead">Jouez, amusez-vous et gagnez des points de fidélité pour vos prochaines commandes !</p>
        
        <?php if (class_exists('UserSession') && UserSession::isAuthenticated()): ?>
            <div class="d-inline-block mt-3" style="background: linear-gradient(135deg, #FFD700, #FDB931); padding: 10px 25px; border-radius: 30px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                <?php
                require_once __DIR__ . '/../config/database.php';
                $db_header = Database::getInstance();
                
                $session_id = $_SESSION['user']['id'] ?? $_SESSION['user']['user_id'] ?? 0;
                
                $stmt_pts = $db_header->prepare("SELECT points FROM users WHERE user_id = :id");
                $stmt_pts->execute(['id' => $session_id]);
                $current_points = $stmt_pts->fetchColumn() ?: 0;
                ?>
                <span style="
                font-weight: 700;
                color: #5a3d00; 
                font-size: 1.1rem;">
                    Votre solde actuel: 🪙 <?= $current_points ?> pts
                </span>
            </div>
        <?php else: ?>
            <div class="alert alert-warning mt-3 d-inline-block rounded-pill">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> Connectez-vous pour gagner des points de fidélité !
            </div>
        <?php endif; ?>
    </div>

    <?php
    $user_id_param = "";
    $fidelity_id_param = "";
    
    if (class_exists('UserSession') && UserSession::isAuthenticated()) {
        $session_id = $_SESSION['user']['id'] ?? $_SESSION['user']['user_id'] ?? null;
        if ($session_id) {
            $user_id_param = "?fidelityId=" . $session_id; 
            $fidelity_id_param = "?fidelityId=" . $session_id;
        }
    }
    ?>

    <div class="row justify-content-center g-5">
        
        <div class="col-md-5">
            <div class="card h-100 border-0 shadow-lg" style="border-radius: 20px; overflow: hidden; transition: transform 0.3s;">
                <div style="height: 200px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-grid-3x3 text-white" style="font-size: 5rem; opacity: 0.8;"></i>
                </div>
                <div class="card-body p-4 text-center">
                    <h3 class="fw-bold mb-3" style="font-family: 'Fredoka', sans-serif;">Créateur de Mosaïque</h3>
                    <p class="text-muted mb-4">Laissez libre cours à votre imagination. Placez les briques sur la grille et créez des œuvres d'art uniques en Lego.</p>
                    <a href="http://localhost:3000/<?= $user_id_param ?>" class="btn w-100 py-3 fw-bold" style="background-color: #4facfe; color: white; border-radius: 10px; font-size: 1.1rem;">
                        <i class="bi bi-play-fill me-2"></i> Jouer maintenant
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card h-100 border-0 shadow-lg" style="border-radius: 20px; overflow: hidden; transition: transform 0.3s;">
                <div style="height: 200px; background: linear-gradient(135deg, #ff0844 0%, #ffb199 100%); display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-bricks text-white" style="font-size: 5rem; opacity: 0.8;"></i>
                </div>
                <div class="card-body p-4 text-center">
                    <h3 class="fw-bold mb-3" style="font-family: 'Fredoka', sans-serif;">Casse-Briques Lego</h3>
                    <p class="text-muted mb-4">Un défi de réflexion ! Placez les blocs aléatoires pour compléter des lignes, détruisez-les et accumulez un maximum de points.</p>
                    <a href="http://localhost:3001/<?= $fidelity_id_param ?>" class="btn w-100 py-3 fw-bold" style="background-color: #ff0844; color: white; border-radius: 10px; font-size: 1.1rem;">
                        <i class="bi bi-play-fill me-2"></i> Jouer maintenant
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.15) !important;
    }
</style>

<?php 
$content = ob_get_clean(); 
require 'layout.php'; 
?>