<?php
require_once 'config/database.php';
try {
    $db = Database::getInstance();
    echo "З'єднання з базою успішне!";
} catch (Exception $e) {
    echo "Помилка: " . $e->getMessage();
}