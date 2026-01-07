<?php ob_start(); ?>
    <h2 class="mb-4">My Orders</h2>

<?php if (empty($orders)): ?>
    <div class="alert alert-info">You haven't placed any orders yet.</div>
    <a href="index.php?page=home" class="btn btn-primary">Create my first mosaic</a>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
            <tr>
                <th>Reference</th>
                <th>Date</th>
                <th>Preview</th>
                <th>Details</th>
                <th>Price</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td class="fw-bold"><?= htmlspecialchars($order['order_number']) ?></td>
                    <td><?= date('d/m/Y', strtotime($order['created_at'])) ?></td>
                    <td>
                        <img src="image.php?id=<?= $order['id_upload'] ?>"
                             alt="Mosaic"
                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; filter: <?= htmlspecialchars($order['filter_used']) ?>;">
                    </td>
                    <td>
                        <span class="badge bg-secondary"><?= htmlspecialchars($order['size_option']) ?>x<?= htmlspecialchars($order['size_option']) ?></span>
                        <small class="text-muted d-block"><?= htmlspecialchars($order['filter_used']) ?></small>
                    </td>
                    <td class="fw-bold"><?= htmlspecialchars($order['total_amount']) ?> €</td>
                    <td>
                        <?php if($order['status'] === 'paid'): ?>
                            <span class="badge bg-success">Paid</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark"><?= htmlspecialchars($order['status']) ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>