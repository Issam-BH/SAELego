<?php
require_once 'config/database.php';
try {
    $db = Database::getInstance();
    echo "Conenction acces";
} catch (Exception $e) {
    echo "Connection error: " . $e->getMessage();
}