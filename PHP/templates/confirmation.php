<?php ob_start(); ?>
<div class="container mt-5 text-center">
    <h1 class="text-success">Merci pour votre achat !</h1>
    <p>Votre commande n°<?= htmlspecialchars($order['order_number']) ?> est validée.</p>
    <div class="mt-4">
        <form action="index.php?page=download" method="POST" class="d-inline">
            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
            <input type="hidden" name="type" value="csv">
            <button type="submit" class="btn btn-primary">Télécharger CSV</button>
        </form>
        <form action="index.php?page=download" method="POST" class="d-inline">
            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
            <input type="hidden" name="type" value="svg">
            <button type="submit" class="btn btn-primary">Télécharger SVG</button>
        </form>
    </div>
</div>
<?php $content = ob_get_clean(); require 'layout.php'; ?>