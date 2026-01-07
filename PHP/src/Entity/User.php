<?php
class User {
    private $id_user;
    private $username;
    private $email;
    private $phone_number;
    private $birth_year;
    private $address;
    private $firstname;
    private $lastname;
    private $password;
    private $role;
    private $two_factor_code;
    private $two_factor_expires_at;

    public function __construct(array $data = []) {
        if (!empty($data)) $this->hydrate($data);
    }

    private function hydrate(array $data) {
        foreach ($data as $key => $value) {
            $method = 'set' . str_replace('_', '', ucwords($key, '_'));
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }


    public function getIdUser() { return $this->id_user; }
    public function getUsername() { return $this->username; }
    public function getEmail() { return $this->email; }
    public function getPhoneNumber() { return $this->phone_number; }
    public function getBirthYear() { return $this->birth_year; }
    public function getAddress() { return $this->address; }
    public function getFirstname() { return $this->firstname; }
    public function getLastname() { return $this->lastname; }
    public function getPassword() { return $this->password; }
    public function getRole() { return $this->role; }
    public function getTwoFactorCode() { return $this->two_factor_code; }
    public function getTwoFactorExpiresAt() { return $this->two_factor_expires_at; }

    public function setIdUser($id) { $this->id_user = $id; }
    public function setUsername($u) { $this->username = $u; }
    public function setEmail($e) { $this->email = $e; }
    public function setPhoneNumber($p) { $this->phone_number = $p; }
    public function setBirthYear($b) { $this->birth_year = $b; }
    public function setAddress($a) { $this->address = $a; }
    public function setFirstname($f) { $this->firstname = $f; }
    public function setLastname($l) { $this->lastname = $l; }
    public function setPassword($p) { $this->password = $p; }
    public function setRole($r) { $this->role = $r; }
    public function setTwoFactorCode($c) { $this->two_factor_code = $c; }
    public function setTwoFactorExpiresAt($d) { $this->two_factor_expires_at = $d; }
}