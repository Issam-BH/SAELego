<?php ob_start(); ?>

<style>
    /* Styles Résultats */
    .mosaic-selection-container {
        display: flex;
        justify-content: center;
        gap: 20px;
        flex-wrap: wrap;
        margin-bottom: 40px;
    }
    .mosaic-card {
        background: white;
        border: 4px solid transparent;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        width: 300px;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        position: relative;
        overflow: hidden;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
    }
    .mosaic-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.15);
    }
    .mosaic-card.selected {
        border-color: #E91E63; 
        transform: scale(1.02);
        box-shadow: 0 0 0 4px rgba(233, 30, 99, 0.2);
    }
    .mosaic-preview {
        padding: 20px;
        background: #f8f9fa;
        flex-grow: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .mosaic-info {
        padding: 15px;
        text-align: center;
        background: white;
    }
    .mosaic-label {
        background: #E0E0E0;
        color: #333;
        font-family: 'Fredoka', sans-serif;
        font-weight: 600;
        font-size: 1.1rem;
        padding: 12px;
        text-align: center;
        text-transform: capitalize;
        border-top: 1px solid #ccc;
    }
    .price-tag {
        font-family: 'Fredoka', sans-serif;
        font-size: 1.6rem;
        color: #333;
        margin-bottom: 5px;
    }
    .brick-count {
        font-size: 0.9rem;
        color: #666;
    }
    .btn-choose-big {
        background-color: #33FF77;
        color: #222;
        font-family: 'Fredoka', sans-serif;
        font-size: 1.5rem;
        padding: 15px 60px;
        border-radius: 50px;
        border: none;
        box-shadow: 0 4px 10px rgba(51, 255, 119, 0.4);
        transition: all 0.2s;
        cursor: not-allowed;
        opacity: 0.5;
        font-weight: 600;
    }
    .btn-choose-big.active {
        cursor: pointer;
        opacity: 1;
    }
    .btn-choose-big.active:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(51, 255, 119, 0.6);
        background-color: #2ce66b;
    }
    .selected-overlay {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #E91E63;
        color: white;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: none;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    .mosaic-card.selected .selected-overlay {
        display: flex;
    }
</style>

<div class="container mt-4">
    <div class="text-center mb-5">
        <h2 class="display-6 fw-bold text-primary">Résultats de la transformation</h2>
        <p class="text-muted">Sélectionnez votre style préféré ci-dessous.</p>
    </div>

    <?php if (!empty($mosaics)): ?>
        
        <form action="index.php?page=order" method="POST" id="selectionForm">
            <input type="hidden" name="id_upload" value="<?= $uploadId ?? '' ?>">
            <input type="hidden" name="size" value="64">
            <input type="hidden" name="brick_data" id="input_brick_data" value="">
            <input type="hidden" name="price" id="input_price" value="">
            <input type="hidden" name="filter_css" id="input_filter" value="">

            <div class="mosaic-selection-container">
                <?php foreach ($mosaics as $key => $m): ?>
                    <?php 
                        if ($key === 'economic') continue; 
                        $priceVal = $m['cost'] / 100;
                        $safeJson = htmlspecialchars(json_encode($m['data']), ENT_QUOTES, 'UTF-8');
                    ?>

                    <div class="mosaic-card" 
                         onclick="selectMosaic(this)" 
                         data-json="<?= $safeJson ?>"
                         data-price="<?= $priceVal ?>">
                        
                        <div class="selected-overlay"><i class="bi bi-check-lg"></i></div>

                        <div class="mosaic-preview">
                            <svg viewBox="0 0 64 64" width="100%" height="auto" style="max-height: 220px; filter: drop-shadow(0px 5px 10px rgba(0,0,0,0.1));">
                                <?php foreach ($m['data'] as $b): ?>
                                    <?php 
                                        $width = ($b['rot'] % 2 == 0) ? $b['w'] : $b['h'];
                                        $height = ($b['rot'] % 2 == 0) ? $b['h'] : $b['w'];
                                    ?>
                                    <rect x="<?= $b['x'] ?>" y="<?= $b['y'] ?>" width="<?= $width ?>" height="<?= $height ?>" fill="<?= $b['color'] ?>" stroke="rgba(0,0,0,0.05)" stroke-width="0.05"/>
                                <?php endforeach; ?>
                            </svg>
                        </div>
                        
                        <div class="mosaic-info">
                            <div class="price-tag"><?= number_format($priceVal, 2) ?> €</div>
                            <div class="brick-count text-muted small"><i class="bi bi-grid-3x3 me-1"></i><?= $m['count'] ?> pièces</div>
                        </div>

                        <div class="mosaic-label">
                            <?= htmlspecialchars($m['label']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="text-center mt-5">
                <button type="submit" id="btnSubmit" class="btn-choose-big" disabled>
                    CHOISIR CE MODÈLE
                </button>
            </div>
        </form>

    <?php else: ?>
        <div class="alert alert-warning text-center p-5 rounded-4 shadow-sm">
            <i class="bi bi-exclamation-triangle display-4 text-warning mb-3"></i>
            <h4 class="alert-heading">La génération a échoué</h4>
            <p>Impossible de créer la mosaïque. L'image est peut-être invalide ou la session a expiré.</p>
            <hr>
            <a href="index.php?page=home" class="btn btn-outline-dark">Réessayer avec une autre image</a>
        </div>
    <?php endif; ?>
</div>

<script>
    function selectMosaic(cardElement) {
        // Reset
        document.querySelectorAll('.mosaic-card').forEach(el => el.classList.remove('selected'));
        // Active
        cardElement.classList.add('selected');

        // Data
        const jsonData = cardElement.dataset.json;
        const price = cardElement.dataset.price;

        // Fill inputs
        document.getElementById('input_brick_data').value = jsonData;
        document.getElementById('input_price').value = price;

        // Enable Button
        const btn = document.getElementById('btnSubmit');
        btn.disabled = false;
        btn.classList.add('active');
    }
</script>

<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>