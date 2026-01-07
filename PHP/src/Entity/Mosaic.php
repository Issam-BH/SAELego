<?php
class Mosaic {
    private $id;
    private $uploads_id;
    private $filter_used;
    private $size_option;
    private $estimated_price;

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
    public function getFilterUsed() { return $this->filter_used; }
    public function getSizeOption() { return $this->size_option; }
    public function getEstimatedPrice() { return $this->estimated_price; }

    public function setId($id) { $this->id = $id; }
    public function setUploadsId($id) { $this->uploads_id = $id; }
    public function setFilterUsed($f) { $this->filter_used = $f; }
    public function setSizeOption($s) { $this->size_option = $s; }
    public function setEstimatedPrice($p) { $this->estimated_price = $p; }
}