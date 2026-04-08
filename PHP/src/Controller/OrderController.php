<?php
require_once __DIR__ . '/../Service/UserSession.php';
require_once __DIR__ . '/../Service/EmailService.php';
require_once __DIR__ . '/../../config/database.php';

class OrderController {

    public function form() {
        if (!UserSession::isAuthenticated()) {
            $_SESSION['redirect_after_login'] = 'index.php?page=order';
            $_SESSION['pending_order_data'] = $_POST;
            header('Location: index.php?page=login');
            exit;
        }

        $orderData = $_SESSION['pending_order_data'] ?? $_POST;
        unset($_SESSION['pending_order_data']);

        if (isset($orderData['id_upload'])) {
            $uploadId = $orderData['id_upload'];
            $brickData = $orderData['brick_data'] ?? '[]';
            $price = $orderData['total_price'] ?? $orderData['price'] ?? 0;
            $size = $orderData['size_option'] ?? $orderData['size'] ?? 64;
            $filter = $orderData['filter_css'] ?? 'standard';

            $pdo = Database::getInstance();
            $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
            $stmt->execute([UserSession::getUserId()]);
            $user = $stmt->fetch();

            require __DIR__ . '/../../templates/order.php';
        } else {
            header('Location: index.php?page=home');
        }
    }

    public function process() {
        if (!UserSession::isAuthenticated()) {
            header('Location: index.php?page=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pdo = Database::getInstance();
            $userId = UserSession::getUserId();

            $basePrice = (float) $_POST['total_price'];
            $couponCode = strtoupper(trim($_POST['coupon_code'] ?? ''));
            
            $usedCoins = intval($_POST['used_coins'] ?? 0);
            $discount = 0;
            if ($couponCode === 'LEGO10') $discount = $basePrice * 0.10;
            if ($couponCode === 'LEGO20') $discount = $basePrice * 0.20;
            
            $priceAfterCoupon = $basePrice - $discount;

            if ($usedCoins > 0) {
                if ($usedCoins > $priceAfterCoupon) {
                    $usedCoins = floor($priceAfterCoupon);
                }

                $stmt_deduct = $pdo->prepare("UPDATE users SET coins = coins - :coins WHERE user_id = :uid AND coins >= :coins_check");
                $stmt_deduct->execute([
                    ':coins' => $usedCoins,
                    ':coins_check' => $usedCoins,
                    ':uid' => $userId
                ]);

                if ($stmt_deduct->rowCount() === 0) {
                    $_SESSION['error'] = "Erreur : Solde de coins insuffisant.";
                    header('Location: index.php?page=order'); 
                    exit;
                }
            }

            $finalPrice = $priceAfterCoupon - $usedCoins;
            if ($finalPrice < 0) $finalPrice = 0; 

            $paymentMethod = $_POST['payment_method'] === 'paypal' ? 'paypal_sandbox' : 'card';

            $sqlAddr = "INSERT INTO address (user_id, line1, city, postal_code, country, is_default) VALUES (:uid, :line1, :city, :cp, :country, 1)";
            $stmt = $pdo->prepare($sqlAddr);
            $stmt->execute([
                ':uid' => $userId, 
                ':line1' => $_POST['address'], 
                ':city' => $_POST['city'], 
                ':cp' => $_POST['zip'], 
                ':country' => $_POST['country'] ?? 'FR'
            ]);
            $addressId = $pdo->lastInsertId();

            $sqlMosaic = "INSERT INTO mosaic (uploads_id, filter_used, size_option, estimated_price, brick_data, created_at) VALUES (:uid, :filter, :size, :price, :data, NOW())";
            $stmt = $pdo->prepare($sqlMosaic);
            $stmt->execute([
                ':uid' => $_POST['upload_id'], 
                ':filter' => $_POST['filter_css'] ?? 'standard', 
                ':size' => $_POST['size_option'] ?? 64, 
                ':price' => $finalPrice, 
                ':data' => $_POST['brick_data'] ?? null
            ]);
            $mosaicId = $pdo->lastInsertId();

            $orderRef = 'CMD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
            $sqlOrder = "INSERT INTO orders (user_id, mosaic_id, shipping_address_id, order_number, status, total_amount, payment_method, created_at) VALUES (:uid, :mid, :aid, :ref, 'paid', :total, :pay, NOW())";
            $stmt = $pdo->prepare($sqlOrder);
            $stmt->execute([
                ':uid' => $userId, 
                ':mid' => $mosaicId, 
                ':aid' => $addressId, 
                ':ref' => $orderRef, 
                ':total' => $finalPrice,
                ':pay' => $paymentMethod
            ]);

            $stmtUser = $pdo->prepare("SELECT email, nickname FROM users WHERE user_id = ?");
            $stmtUser->execute([$userId]);
            $user = $stmtUser->fetch();

            $emailBody = "
                <div style='font-family: Arial, sans-serif; color: #333;'>
                    <h2>Merci pour votre achat {$user['nickname']} !</h2>
                    <p>Votre commande <strong>$orderRef</strong> a été validée avec succès.</p>
                    <p>Montant total payé : <strong>" . number_format($finalPrice, 2) . " €</strong>.</p>";
                    
            if ($usedCoins > 0) {
                $emailBody .= "<p>Coins utilisés : <strong>$usedCoins</strong></p>";
            }
            
            $emailBody .= "
                    <p>Moyen de paiement : <strong>" . strtoupper($paymentMethod) . "</strong></p>
                    <hr>
                    <p>Vous pouvez dès à présent télécharger vos plans de construction et inventaires CSV depuis votre espace client.</p>
                </div>
            ";
            EmailService::sendEmail($user['email'], "Confirmation de commande - $orderRef", $emailBody);

            header("Location: index.php?page=confirmation&ref=" . $orderRef);
            exit;
        }
    }

    public function confirmation() {
        if (!isset($_GET['ref'])) header('Location: index.php');
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_number = ?");
        $stmt->execute([$_GET['ref']]);
        $order = $stmt->fetch();
        require __DIR__ . '/../../templates/confirmation.php';
    }

    public function history() {
        if (!UserSession::isAuthenticated()) { header('Location: index.php?page=login'); exit; }
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT o.*, m.size_option, m.filter_used, m.uploads_id 
                               FROM orders o 
                               JOIN mosaic m ON o.mosaic_id = m.id 
                               WHERE o.user_id = ? 
                               ORDER BY o.created_at DESC");
        
        $stmt->execute([UserSession::getUserId()]);
        $orders = $stmt->fetchAll();
        require __DIR__ . '/../../templates/history.php';
    }
}