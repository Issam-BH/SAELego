<?php ob_start(); ?>
<div class="container mt-4">
    <h2 class="mb-4 text-center">Choisissez votre Mosaïque</h2>

    <?php if (!empty($mosaics)): ?>
        
        <ul class="nav nav-tabs" id="mosaicTabs" role="tablist">
            <?php $i = 0; foreach ($mosaics as $key => $m): ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $i === 0 ? 'active' : '' ?>" 
                            id="<?= $key ?>-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#<?= $key ?>" 
                            type="button" role="tab">
                        <?= htmlspecialchars($m['label']) ?>
                    </button>
                </li>
            <?php $i++; endforeach; ?>
        </ul>

        <div class="tab-content border border-top-0 p-4 bg-white shadow-sm" id="mosaicTabContent">
            <?php $i = 0; foreach ($mosaics as $key => $m): ?>
                <div class="tab-pane fade <?= $i === 0 ? 'show active' : '' ?>" id="<?= $key ?>" role="tabpanel">
                    
                    <div class="row align-items-center">
                        <div class="col-md-7 text-center">
                            <svg viewBox="0 0 64 64" width="100%" height="auto" style="background: #eee; border: 1px solid #ccc; max-height: 500px;">
                                <?php foreach ($m['data'] as $b): ?>
                                    <?php 
                                        $width = ($b['rot'] % 2 == 0) ? $b['w'] : $b['h'];
                                        $height = ($b['rot'] % 2 == 0) ? $b['h'] : $b['w'];
                                    ?>
                                    <rect x="<?= $b['x'] ?>" y="<?= $b['y'] ?>" width="<?= $width ?>" height="<?= $height ?>" fill="<?= $b['color'] ?>" stroke="#000" stroke-width="0.05"/>
                                <?php endforeach; ?>
                            </svg>
                        </div>

                        <div class="col-md-5">
                            <h4 class="mt-3 mt-md-0"><?= htmlspecialchars($m['label']) ?></h4>
                            <hr>
                            
                            <div class="mb-4">
                                <h2 class="text-primary fw-bold">
                                    <?= number_format($m['cost'] / 100, 2) ?> €
                                </h2>
                                <p class="lead">
                                    <i class="bi bi-grid-3x3"></i> <strong><?= $m['count'] ?></strong> pièces LEGO®
                                </p>
                                <small class="text-muted">Score de précision : <?= $m['error'] ?></small>
                            </div>

                            <form action="index.php?page=order" method="POST" class="d-grid gap-2">
                                <input type="hidden" name="id_upload" value="<?= $uploadId ?>">
                                <input type="hidden" name="brick_data" value="<?= htmlspecialchars(json_encode($m['data'])) ?>">
                                <input type="hidden" name="price" value="<?= $m['cost'] / 100 ?>">
                                <input type="hidden" name="size" value="64">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="bi bi-cart-check"></i> Choisir ce modèle
                                </button>
                            </form>

                            <div class="mt-4">
                                <h6>Télécharger :</h6>
                                <div class="btn-group w-100">
                                    <form action="index.php?page=download" method="POST" target="_blank" class="btn-group w-100">
                                        <input type="hidden" name="brick_data" value="<?= htmlspecialchars(json_encode($m['data'])) ?>">
                                        
                                        <button type="submit" name="type" value="csv" class="btn btn-outline-secondary">
                                            <i class="bi bi-file-earmark-spreadsheet"></i> CSV
                                        </button>
                                        <button type="submit" name="type" value="svg" class="btn btn-outline-secondary">
                                            <i class="bi bi-card-image"></i> SVG
                                        </button>
                                    </form>
                                    <button onclick="window.print()" class="btn btn-outline-dark">
                                        <i class="bi bi-printer"></i> PDF
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            <?php $i++; endforeach; ?>
        </div>

    <?php else: ?>
        <div class="alert alert-danger text-center">
            La génération a échoué. Aucun fichier de sortie n'a été trouvé.
        </div>
    <?php endif; ?>
</div>

<script>
    var triggerTabList = [].slice.call(document.querySelectorAll('#mosaicTabs button'))
    triggerTabList.forEach(function (triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl)
        triggerEl.addEventListener('click', function (event) {
            event.preventDefault()
            tabTrigger.show()
        })
    })
</script>

<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>