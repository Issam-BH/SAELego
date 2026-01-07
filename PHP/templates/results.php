<?php ob_start(); ?>
    <h2 class="mb-4">Here are your generated mosaics</h2>

<?php if (!empty($variants) && isset($image)): ?>
    <div class="row">
        <?php foreach ($variants as $index => $variant): ?>
            <div class="col-md-4 text-center">
                <div class="card mb-4 shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= $variant['name'] ?></h5>

                        <div style="overflow: hidden; border-radius: 5px;">
                            <img src="image.php?id=<?= $image['id_upload'] ?>"
                                 class="img-fluid"
                                 style="filter: <?= $variant['filter'] ?>; width: 100%;">
                        </div>

                        <ul class="list-unstyled mt-3 mb-4 flex-grow-1">
                            <li>Estimated price: <strong><?= $variant['price'] ?> €</strong></li>
                        </ul>

                        <form action="index.php?page=order" method="POST" class="mt-auto">
                            <input type="hidden" name="id_upload" value="<?= $image['id_upload'] ?>">
                            <input type="hidden" name="filter" value="<?= htmlspecialchars($variant['filter']) ?>">
                            <input type="hidden" name="price" value="<?= $variant['price'] ?>">
                            <input type="hidden" name="size" value="64">
                            <button type="submit" class="btn btn-outline-primary w-100">Order</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="alert alert-warning">No results to display. Please restart the upload process.</div>
    <a href="index.php?page=home" class="btn btn-primary">Back to Home</a>
<?php endif; ?>

<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>