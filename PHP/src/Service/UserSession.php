<?php
class UserSession {
    private static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function login($idUser, $username, $role = 'user') {
        self::start();
        $_SESSION['user'] = [
            'id' => $idUser,
            'username' => $username,
            'role' => $role
        ];
    }

    public static function logout() {
        self::start();
        unset($_SESSION['user']);
        session_destroy();
    }

    public static function getUserId() {
        self::start();
        return $_SESSION['user']['id'] ?? null;
    }

    public static function isAuthenticated() {
        self::start();
        return isset($_SESSION['user']['id']);
    }

    public static function getRole() {
        self::start();
        return $_SESSION['user']['role'] ?? 'user';
    }
}