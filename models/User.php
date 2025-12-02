<?php

class User {
    private $id;
    private $name;
    
    public function __construct($id = null, $name = '') {
        $this->id = $id;
        $this->name = $name;
    }
    
    // Геттеры
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    
    // Сеттеры
    public function setId($id) { $this->id = $id; }
    public function setName($name) { $this->name = $name; }
}
?>