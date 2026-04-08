<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php';

try {
    $db = Database::getInstance();
    
    // --- 1. GET Points (GET ask) ---
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (!isset($_GET['user_id'])) {
            echo json_encode(['error' => 'user_id is missing']);
            exit();
        }
        
        $user_id = intval($_GET['user_id']);
        
        $stmt = $db->prepare("SELECT points FROM users WHERE user_id = :id");
        $stmt->execute(['id' => $user_id]);
        $result = $stmt->fetch();
        
        if ($result) {
            echo json_encode(['success' => true, 'points' => intval($result['points'])]);
        } else {
            echo json_encode(['error' => 'User not found']);
        }
        exit();
    }
    
    // --- 2. Add points (POST ask) ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Getting JSON-data
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['user_id']) || !isset($input['add_points'])) {
            echo json_encode(['error' => 'user_id or add_points is missing']);
            exit();
        }
        
        $user_id = intval($input['user_id']);
        $points_to_add = intval($input['add_points']);
        
        // Update for user
        $stmt = $db->prepare("UPDATE users SET points = points + :points WHERE user_id = :id");
        $stmt->execute([
            'points' => $points_to_add,
            'id' => $user_id
        ]);
        
        $stmt = $db->prepare("SELECT points FROM users WHERE user_id = :id");
        $stmt->execute(['id' => $user_id]);
        $new_points = $stmt->fetchColumn();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Points added',
            'new_total' => intval($new_points)
        ]);
        exit();
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>