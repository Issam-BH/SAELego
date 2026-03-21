<?php ob_start(); ?>
<div class="container mt-4">
    <h2 class="mb-4 text-center fw-bold">Choisissez votre algorithme de pavage</h2>
    <div class="alert alert-info text-center">
        Taille de la plaque : <strong><?= htmlspecialchars($sizeOption ?? 64) ?>x<?= htmlspecialchars($sizeOption ?? 64) ?></strong> picots.<br>
        La photo de la mosaique, la liste des pièces et le plan de montage seront débloqués après validation de votre commande.
    </div>

    <?php if (empty($mosaicsAlgorithms)): ?>
        <div class="alert alert-danger">Aucun résultat n'a pu être généré par le moteur de pavage.</div>
    <?php else: ?>
        <div class="row g-4 justify-content-center">
            <?php foreach ($mosaicsAlgorithms as $key => $algo): 
                $price = $algo['count'] * 0.15; // Prix dynamique (0.15€ par brique)
                // Calcul de la taille visuelle pour l'aperçu : 1 brique = 6 pixels (ajustable)
                $visualSize = $sizeOption * 6; 
            ?>
                <div class="col-md-6 col-lg-5"> <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                        <div class="card-header bg-dark text-white text-center py-3">
                            <h5 class="mb-0"><?= htmlspecialchars($algo['label']) ?></h5>
                        </div>
                        
                        <div class="card-body bg-light d-flex flex-column align-items-center justify-content-center p-4">
                            <div style="width: <?= $visualSize ?>px; max-width: 100%; aspect-ratio: 1/1; border: 2px solid #333; background: #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                                <svg viewBox="0 0 <?= htmlspecialchars($sizeOption) ?> <?= htmlspecialchars($sizeOption) ?>" width="100%" height="100%" style="shape-rendering: crispEdges; display: block;">
                                    <?php foreach ($algo['data'] as $b): 
                                        $w = ($b['rot'] % 2 == 0) ? $b['w'] : $b['h'];
                                        $h = ($b['rot'] % 2 == 0) ? $b['h'] : $b['w'];
                                    ?>
                                        <rect x="<?= $b['x'] ?>" y="<?= $b['y'] ?>" width="<?= $w ?>" height="<?= $h ?>" fill="<?= $b['color'] ?>" stroke="#000" stroke-width="0.05" />
                                    <?php endforeach; ?>
                                </svg>
                            </div>
                            
                            <hr class="w-100 mt-4">
                            <p class="mb-1 text-muted">Nombre de pièces : <strong><?= $algo['count'] ?></strong></p>
                            <h4 class="text-success fw-bold"><?= number_format($price, 2) ?> €</h4>
                        </div>
                        
                        <div class="card-footer bg-white border-0 p-3 mt-auto">
                            <form action="index.php?page=order" method="POST">
                                <input type="hidden" name="id_upload" value="<?= htmlspecialchars($uploadId) ?>">
                                <input type="hidden" name="filter_css" value="<?= htmlspecialchars($key) ?>">
                                <input type="hidden" name="size_option" value="<?= htmlspecialchars($sizeOption) ?>">
                                <input type="hidden" name="brick_data" value='<?= htmlspecialchars(json_encode($algo['data']), ENT_QUOTES, 'UTF-8') ?>'>
                                <input type="hidden" name="total_price" value="<?= $price ?>">
                                
                                <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm">
                                    Commander ce modèle
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <div class="mt-4 text-center">
        <a href="index.php?page=home" class="btn btn-outline-secondary px-5 py-2">Retour à l'accueil</a>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>