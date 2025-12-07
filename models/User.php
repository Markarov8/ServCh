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

    public static function getAll() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT * FROM users ORDER BY id");
        $users = [];
        while ($row = $stmt->fetch()) {
            $users[] = new User($row['id'], $row['name']);
        }
        return $users;
    }

    public static function findById($id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) return null;
        return new User($row['id'], $row['name']);
    }

    public function save() {
        $db = Database::getInstance()->getConnection();
        if ($this->id === null) {
            $stmt = $db->prepare("INSERT INTO users (name) VALUES (?)");
            $stmt->execute([$this->name]);
            $this->id = $db->lastInsertId();
        } 
        else {
            $stmt = $db->prepare("UPDATE users SET name = ? WHERE id = ?");
            $stmt->execute([$this->name, $this->id]);
        }
    }
}
?>