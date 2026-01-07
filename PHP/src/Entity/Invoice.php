<?php
class Invoice {
    private $id;
    private $order_id;
    private $invoice_number;
    private $content_json;
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
    public function getOrderId() { return $this->order_id; }
    public function getInvoiceNumber() { return $this->invoice_number; }
    public function getContentJson() { return $this->content_json; }
    public function getCreatedAt() { return $this->created_at; }

    public function getContent() {
        return json_decode($this->content_json, true);
    }

    public function setId($id) { $this->id = $id; }
    public function setOrderId($id) { $this->order_id = $id; }
    public function setInvoiceNumber($n) { $this->invoice_number = $n; }
    public function setContentJson($j) { $this->content_json = $j; }
    public function setCreatedAt($d) { $this->created_at = $d; }
}