<?php
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$device_id = $data['device_id'] ?? null;
$user_id = $data['user_id'] ?? null;

if (!$device_id) {
    echo json_encode(['success' => false, 'message' => 'device_id manquant']);
    exit;
}
try {
    $pdo = Database::getInstance();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur connexion BDD : ' . $e->getMessage()]);
    exit;
}

$stmt = $pdo->prepare("
    INSERT INTO app_devices (device_id, user_id, last_ping) 
    VALUES (?, ?, NOW()) 
    ON DUPLICATE KEY UPDATE last_ping = NOW(), user_id = ?
");
$stmt->execute([$device_id, $user_id, $user_id]);

$response = [
    'success' => true,
    'notifications' => []
];

$response['notifications']['daily_image'] = [
    'id' => 1,
    'title' => 'Nouvelle Mosaïques ?',
    'message' => 'Venez faire une nouvelle mosaïque ! ',
    'url' => 'http://10.0.2.2/php/public/index.php?page=home'
];

if ($user_id) {
    $stmt = $pdo->prepare("SELECT MAX(created_at) as last_order FROM orders WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $last_order = $stmt->fetchColumn();

    if ($last_order) {
        $days_since_order = (strtotime('now') - strtotime($last_order)) / (60 * 60 * 24);
        if ($days_since_order > 30) {
            $response['notifications']['loyalty'] = [
                'id' => 2,
                'title' => 'Vous nous manquez !',
                'message' => 'Revenez jouer pour gagner des points de fidélité !',
                'url' => 'http://10.0.2.2:3000'
            ];
        }
    }
}

echo json_encode($response);
?>