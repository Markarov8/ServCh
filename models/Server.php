<?php

class Server {
    private $id;
    private $name;
    private $temperature;
    private $weatherCondition;
    private $windSpeed;
    private $humidity;
    private $timestamp;
    
    public function __construct($id = null, $name = '', $temperature = 0, $weatherCondition = '', $windSpeed = 0, $humidity = 0, $timestamp = null) {
        $this->id = $id;
        $this->name = $name;
        $this->temperature = $temperature;
        $this->weatherCondition = $weatherCondition;
        $this->windSpeed = $windSpeed;
        $this->humidity = $humidity;
        $this->timestamp = $timestamp;
    }
    
    // Геттеры
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getTemperature() { return $this->temperature; }
    public function getWeatherCondition() { return $this->weatherCondition; }
    public function getWindSpeed() { return $this->windSpeed; }
    public function getHumidity() { return $this->humidity; }
    public function getTimestamp() { return $this->timestamp; }
    
    // Сеттеры
    public function setId($id) { $this->id = $id; }
    public function setName($name) { $this->name = $name; }
    public function setTemperature($temperature) { $this->temperature = $temperature; }
    public function setWeatherCondition($weatherCondition) { $this->weatherCondition = $weatherCondition; }
    public function setWindSpeed($windSpeed) { $this->windSpeed = $windSpeed; }
    public function setHumidity($humidity) { $this->humidity = $humidity; }
    public function setTimestamp($timestamp) { $this->timestamp = $timestamp; }

    public static function getAll() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT * FROM servers ORDER BY id");
        $servers = [];
        while ($row = $stmt->fetch()) {
            $servers[] = new Server(
                $row['id'],
                $row['name'],
                $row['temperature'],
                $row['weather_condition'],
                $row['wind_speed'],
                $row['humidity'],
                $row['timestamp']
            );
        }
        return $servers;
    }

    public static function findById($id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM servers WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) return null;
        return new Server(
            $row['id'],
            $row['name'],
            $row['temperature'],
            $row['weather_condition'],
            $row['wind_speed'],
            $row['humidity'],
            $row['timestamp']
        );
    }

    public function save() {
        $db = Database::getInstance()->getConnection();
        if ($this->id === null) {
            $stmt = $db->prepare("
                INSERT INTO servers (name, temperature, weather_condition, wind_speed, humidity, timestamp)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $this->name,
                $this->temperature,
                $this->weatherCondition,
                $this->windSpeed,
                $this->humidity
            ]);
            $this->id = $db->lastInsertId();
        } 
        else {
            $stmt = $db->prepare("
                UPDATE servers 
                SET name = ?, temperature = ?, weather_condition = ?, wind_speed = ?, humidity = ?, timestamp = NOW()
                WHERE id = ?
            ");
            $stmt->execute([
                $this->name,
                $this->temperature,
                $this->weatherCondition,
                $this->windSpeed,
                $this->humidity,
                $this->id
            ]);
        }
    }

    public static function updateWeather($id, $temp, $condition, $wind, $humidity) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            UPDATE servers 
            SET temperature = ?, weather_condition = ?, wind_speed = ?, humidity = ?, timestamp = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$temp, $condition, $wind, $humidity, $id]);
    }

    public static function generateRandomWeather($id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM servers WHERE id = ?");
        $stmt->execute([$id]);
        $current = $stmt->fetch();
        if (!$current) return false;

        $newTemp = max(-55, min(55, $current['temperature'] + (mt_rand(-30, 30) / 10)));
        $newWind = max(0, min(45, $current['wind_speed'] + (mt_rand(-20, 20) / 10)));
        $newHumidity = max(0, min(100, $current['humidity'] + mt_rand(-10, 10)));

        if ($newTemp < -2){
            $conditions = ['sunny', 'cloudy', 'snowstormy', 'snowy', 'foggy'];
        } 
        else {
            $conditions = ['sunny', 'cloudy', 'rainy', 'stormy', 'foggy'];
        }
        $newCondition = $conditions[array_rand($conditions)];

        $stmt = $db->prepare("
            UPDATE servers 
            SET temperature = ?, weather_condition = ?, wind_speed = ?, humidity = ?, timestamp = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$newTemp, $newCondition, $newWind, $newHumidity, $id]);

        return true;
    }

    public static function logWeatherChange($serverId, $temp, $condition, $wind, $humidity) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            INSERT INTO weather_logs (server_id, temperature, weather_condition, wind_speed, humidity, timestamp)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$serverId, $temp, $condition, $wind, $humidity]);
    }
}
?>