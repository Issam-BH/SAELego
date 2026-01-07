<?php
require_once __DIR__ . '/../Manager/LogManager.php';

class MosaicController {
    public function upload() {
        if (!UserSession::isAuthenticated()) {
            header('Location: index.php?page=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['userImage'])) {
            $file = $_FILES['userImage'];

            if ($file['error'] === UPLOAD_ERR_OK) {

                $imageData = file_get_contents($file['tmp_name']);
                $imageType = $file['type'];
                $fileName  = $file['name'];
                $userId    = UserSession::getUserId();

                $pdo = Database::getInstance();
                $sql = "INSERT INTO uploads (user_id, filename, image_data, image_type, uploaded_at) 
                    VALUES (:uid, :fname, :data, :type, NOW())";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':uid'   => $userId,
                    ':fname' => $fileName,
                    ':data'  => $imageData,
                    ':type'  => $imageType
                ]);

                $lastId = $pdo->lastInsertId();
                $logManager = new LogManager();
                $logManager->log('INFO', 'Upload image BLOB successful');

                header("Location: index.php?page=preview&id_upload=$lastId");
                exit;
            }
        }
        require __DIR__ . '/../../templates/home.php';
    }

    public function preview($id) {
        if (!UserSession::isAuthenticated()) {
            header('Location: index.php?page=login');
            exit;
        }

        $uploadId = $id;
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT filename FROM uploads WHERE id_upload = ?");
        $stmt->execute([$uploadId]);
        $image = $stmt->fetch();

        if (!$image) {
            die("Image introuvable en base de données.");
        }
        require __DIR__ . '/../../templates/preview.php';
    }

    public function results() {
        $variants = [];
        $image = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_upload'])) {

            $uploadId = $_POST['id_upload'];

            $variants = [
                ['name' => 'Bleu Profond', 'filter' => 'hue-rotate(180deg)', 'price' => 45.00],
                ['name' => 'Rouge Vif',    'filter' => 'sepia(1) saturate(3) hue-rotate(-50deg)', 'price' => 48.00],
                ['name' => 'Noir & Blanc', 'filter' => 'grayscale(100%)', 'price' => 35.00],
            ];

            $pdo = Database::getInstance();
            $stmt = $pdo->prepare("SELECT id_upload, filename FROM uploads WHERE id_upload = ?");
            $stmt->execute([$uploadId]);
            $image = $stmt->fetch();

            if (!$image) {
                header('Location: index.php?page=home');
                exit;
            }
            require __DIR__ . '/../../templates/results.php';

        } else {
            header('Location: index.php?page=home');
            exit;
        }
    }
}