<?php
require_once __DIR__ . '/../Manager/LogManager.php';
require_once __DIR__ . '/../Service/MosaicService.php';
require_once __DIR__ . '/../Service/UserSession.php';

class MosaicController {
    
    public function upload() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['userImage'])) {
            $file = $_FILES['userImage'];
            if ($file['error'] === UPLOAD_ERR_OK) {
                $userId = UserSession::getUserId();
                $pdo = Database::getInstance();
                $sql = "INSERT INTO uploads (user_id, filename, image_data, image_type, uploaded_at) VALUES (:uid, :fname, :data, :type, NOW())";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':uid' => $userId, ':fname' => $file['name'], ':data' => file_get_contents($file['tmp_name']), ':type' => $file['type']]);
                header("Location: index.php?page=preview&id_upload=" . $pdo->lastInsertId());
                exit;
            }
        }
        require __DIR__ . '/../../templates/home.php';
    }

    public function preview($id) {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM uploads WHERE id_upload = ?");
        $stmt->execute([$id]);
        $image = $stmt->fetch();
        if (!$image) die("Image introuvable.");
        require __DIR__ . '/../../templates/preview.php';
    }

    public function crop() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            if (isset($data['image'])) {
                $imageParts = explode(";base64,", $data['image']);
                $imageType = explode("image/", $imageParts[0])[1] ?? 'jpeg';
                $imageBase64 = base64_decode($imageParts[1]);

                $pdo = Database::getInstance();
                $sql = "INSERT INTO uploads (user_id, filename, image_data, image_type, uploaded_at) VALUES (:uid, :fname, :data, :type, NOW())";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':uid' => UserSession::getUserId(), 
                    ':fname' => 'cropped_'.uniqid().'.'.$imageType, 
                    ':data' => $imageBase64, 
                    ':type' => 'image/'.$imageType
                ]);

                echo json_encode(['success' => true, 'new_id' => $pdo->lastInsertId()]);
                exit;
            }
        }
        http_response_code(400);
        echo json_encode(['success' => false]);
        exit;
    }

    public function results() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_upload'])) {
            $uploadId = $_POST['id_upload'];
            $sizeOption = $_POST['size_option'] ?? 64;
            
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare("SELECT image_data FROM uploads WHERE id_upload = ?");
            $stmt->execute([$uploadId]);
            $data = $stmt->fetchColumn();

            if ($data) {
                $tmpInputImg = sys_get_temp_dir() . '/input_' . uniqid() . '.jpg';
                file_put_contents($tmpInputImg, $data);

                $service = new MosaicService();
                try {
                    $mosaicsAlgorithms = $service->generateMosaic($tmpInputImg, $sizeOption);
                    require __DIR__ . '/../../templates/results.php';
                } catch (Exception $e) {
                    die("Erreur de génération : " . htmlspecialchars($e->getMessage()));
                } finally {
                    if (file_exists($tmpInputImg)) @unlink($tmpInputImg);
                }
            } else {
                header('Location: index.php?page=home');
            }
        } else {
            header('Location: index.php?page=home');
        }
    }

    public function download() {
        if (!UserSession::isAuthenticated()) {
            header('Location: index.php?page=login');
            exit;
        }

        $orderId = $_POST['order_id'] ?? null;
        $type = $_POST['type'] ?? '';

        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT m.brick_data, m.size_option FROM orders o JOIN mosaic m ON o.mosaic_id = m.id WHERE o.id = ? AND o.user_id = ? AND o.status = 'paid'");
        $stmt->execute([$orderId, UserSession::getUserId()]);
        $result = $stmt->fetch();

        if (!$result || empty($result['brick_data'])) {
            die("Accès refusé : Vous devez acheter cette mosaïque pour en télécharger les plans.");
        }

        $bricks = json_decode($result['brick_data'], true);
        $size = $result['size_option'] ?? 64;

        if ($type === 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="inventaire_lego.csv"');
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Dimension', 'Couleur', 'X', 'Y', 'Rotation']);
            foreach ($bricks as $b) fputcsv($out, [$b['w'].'x'.$b['h'], $b['color'], $b['x'], $b['y'], $b['rot']]);
            fclose($out);
            exit;
        } elseif ($type === 'svg') {
            header('Content-Type: image/svg+xml');
            header('Content-Disposition: attachment; filename="plan_montage.svg"');
            echo '<?xml version="1.0" encoding="UTF-8"?><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 '.$size.' '.$size.'">';
            foreach ($bricks as $b) {
                $w = ($b['rot'] % 2 == 0) ? $b['w'] : $b['h'];
                $h = ($b['rot'] % 2 == 0) ? $b['h'] : $b['w'];
                echo sprintf('<rect x="%s" y="%s" width="%s" height="%s" fill="%s" stroke="#000" stroke-width="0.05"/>', $b['x'], $b['y'], $w, $h, $b['color']);
            }
            echo '</svg>';
            exit;
        }
    }
}