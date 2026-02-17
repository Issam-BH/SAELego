<?php ob_start(); ?>
    <div class="text-center py-5">
        <div class="mb-4">
            <div style="width: 100px; height: 100px; background: #33FF77; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; box-shadow: 0 10px 20px rgba(51, 255, 119, 0.3);">
                <i class="bi bi-check-lg text-white" style="font-size: 3rem;"></i>
            </div>
        </div>

        <h1 class="fw-bold mb-3">Merci pour votre commande !</h1>
        <p class="text-muted lead mb-5">Votre mosaïque est en cours de préparation dans notre usine.</p>

        <div class="card mx-auto shadow-sm border-0" style="max-width: 400px; background: #fff;">
            <div class="card-body p-4">
                <h6 class="text-uppercase text-muted small fw-bold mb-3">Ticket de commande</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span>N° Commande</span>
                    <span class="fw-bold font-monospace"><?= htmlspecialchars($order['order_number']) ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Montant</span>
                    <span class="fw-bold text-success"><?= htmlspecialchars($order['total_amount']) ?> €</span>
                </div>
            </div>
            <div class="card-footer bg-light border-0 py-3">
                <small class="text-muted">Un email de confirmation vient de partir.</small>
            </div>
        </div>

        <div class="mt-5">
            <a href="index.php?page=home" class="btn btn-outline-primary rounded-pill px-4">Retour à l'accueil</a>
            <a href="index.php?page=history" class="btn btn-link text-decoration-none">Voir mes commandes</a>
        </div>
    </div>
<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>