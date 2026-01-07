<?php
require_once __DIR__ . '/../../config/database.php';
class LogManager {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }
    public function log(string $level, string $message, ?int $userId = null): void {
        $this->cleanOldLogs();

        $sql = "INSERT INTO user_log (user_id, level, message, ip_address, created_at) 
                VALUES (:user_id, :level, :message, :ip, NOW())";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':level'   => $level,
            ':message' => $message,
            ':ip'      => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ]);
    }

    private function cleanOldLogs(): void {
        $sql = "DELETE FROM user_log WHERE created_at < DATE_SUB(NOW(), INTERVAL 3 MONTH)";
        $this->pdo->exec($sql);
    }

    public function getAllLogs(): array {
        $stmt = $this->pdo->query("SELECT * FROM user_log ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }
}