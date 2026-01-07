<?php ob_start(); ?>
    <div class="text-center py-5">
        <div class="mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor" class="bi bi-check-circle-fill text-success" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
            </svg>
        </div>

        <h1 class="display-5 fw-bold">Thank you for your order!</h1>
        <p class="lead text-muted">Your mosaic is being prepared.</p>

        <div class="card mx-auto mt-5" style="max-width: 500px;">
            <div class="card-body">
                <h5 class="card-title">Summary</h5>
                <p class="card-text">Order number: <strong><?= htmlspecialchars($order['order_number']) ?></strong></p>
                <p class="card-text">Amount paid: <strong><?= htmlspecialchars($order['total_amount']) ?> €</strong></p>
                <p class="text-success"><small>A confirmation email will be sent to you.</small></p>
            </div>
        </div>

        <div class="mt-4">
            <a href="index.php?page=home" class="btn btn-outline-primary">Back to home page</a>
        </div>
    </div>
<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>