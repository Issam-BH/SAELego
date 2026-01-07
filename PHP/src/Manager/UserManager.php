<?php
require_once __DIR__ . '/../Entity/User.php';

class UserManager {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function verifyPassword($username, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = :u");
        $stmt->execute([':u' => $username]);
        $data = $stmt->fetch();

        if ($data && password_verify($password, $data['password'])) {
            return new User($data);
        }
        return null;
    }

    public function generate2FACode(User $user) {
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $sql = "UPDATE users SET two_factor_code = :code, two_factor_expires_at = DATE_ADD(NOW(), INTERVAL 1 MINUTE) WHERE id_user = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':code' => $code, ':id' => $user->getIdUser()]);
        return $code;
    }

    public function verify2FACode($userId, $code) {
        $sql = "SELECT * FROM users WHERE id_user = :id AND two_factor_code = :code AND two_factor_expires_at > NOW()";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $userId, ':code' => $code]);
        $data = $stmt->fetch();

        if ($data) {
            $this->pdo->prepare("UPDATE users SET two_factor_code = NULL WHERE id_user = ?")->execute([$userId]);
            return new User($data);
        }
        return null;
    }

    public function updateProfile(User $user) {
        $sql = "UPDATE users SET firstname = :first, lastname = :last, email = :email, 
                address = :addr, phone_number = :phone WHERE id_user = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':first' => $user->getFirstname(),
            ':last'  => $user->getLastname(),
            ':email' => $user->getEmail(),
            ':addr'  => $user->getAddress(),
            ':phone' => $user->getPhoneNumber(),
            ':id'    => $user->getIdUser()
        ]);
    }

    public function getUserById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id_user = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        return $data ? new User($data) : null;
    }

    public function register(User $user) {
        $sql = "INSERT INTO users (
                    username, email, password, 
                    firstname, lastname, phone_number, 
                    birth_year, address, role, created_at
                ) VALUES (
                    :username, :email, :password, 
                    :firstname, :lastname, :phone, 
                    :birth, :address, :role, NOW()
                )";

        $stmt = $this->pdo->prepare($sql);

        $algo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_DEFAULT;
        $hash = password_hash($user->getPassword(), $algo);

        return $stmt->execute([
            ':username'  => $user->getUsername(),
            ':email'     => $user->getEmail(),
            ':password'  => $hash,
            ':firstname' => $user->getFirstname(),
            ':lastname'  => $user->getLastname(),
            ':phone'     => $user->getPhoneNumber(),
            ':birth'     => $user->getBirthYear(),
            ':address'   => $user->getAddress(),
            ':role'      => 'user'
        ]);
    }
}