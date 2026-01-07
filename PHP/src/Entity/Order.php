<?php
class Order {
    private $id;
    private $user_id;
    private $mosaic_id;
    private $order_number;
    private $status;
    private $total_amount;
    private $created_at;

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

    public function getId() { return $this->id; }
    public function getOrderNumber() { return $this->order_number; }
    public function getTotalAmount() { return $this->total_amount; }
    public function getStatus() { return $this->status; }
    public function getCreatedAt() { return $this->created_at; }

    public function setId($id) { $this->id = $id; }
    public function setUserId($id) { $this->user_id = $id; }
    public function setMosaicId($id) { $this->mosaic_id = $id; }
    public function setOrderNumber($n) { $this->order_number = $n; }
    public function setTotalAmount($a) { $this->total_amount = $a; }
    public function setStatus($s) { $this->status = $s; }
    public function setCreatedAt($d) { $this->created_at = $d; }
}