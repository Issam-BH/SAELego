<?php ob_start(); ?>
<div class="container">
    <h2 class="mb-4">Mes Commandes</h2>

    <?php if (empty($orders)): ?>
        <div class="text-center py-5 bg-white rounded-4 shadow-sm">
            <i class="bi bi-box-seam text-muted" style="font-size: 3rem;"></i>
            <p class="mt-3 text-muted">Aucune commande pour le moment.</p>
            <a href="index.php?page=home" class="btn btn-primary mt-2">Créer ma première mosaïque</a>
        </div>
    <?php else: ?>
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                    <tr>
                        <th class="py-3 ps-4">Référence</th>
                        <th>Date</th>
                        <th>Aperçu</th>
                        <th>Détails</th>
                        <th>Prix</th>
                        <th class="pe-4 text-end">Statut</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td class="ps-4 fw-bold font-monospace text-primary">#<?= htmlspecialchars($order['order_number']) ?></td>
                            <td class="text-muted"><?= date('d/m/Y', strtotime($order['created_at'])) ?></td>
                            <td>
                                <img src="image.php?id=<?= $order['id_upload'] ?>"
                                     alt="Mosaic"
                                     class="rounded-3 shadow-sm border"
                                     style="width: 48px; height: 48px; object-fit: cover; filter: <?= htmlspecialchars($order['filter_used']) ?>;">
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border"><?= htmlspecialchars($order['size_option']) ?>x<?= htmlspecialchars($order['size_option']) ?></span>
                            </td>
                            <td class="fw-bold"><?= htmlspecialchars($order['total_amount']) ?> €</td>
                            <td class="pe-4 text-end">
                                <?php if($order['status'] === 'paid'): ?>
                                    <span class="badge bg-success-subtle text-success rounded-pill px-3">Payé</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-3"><?= htmlspecialchars($order['status']) ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>