<?php

class WeatherLog {
    private $id;
    private $serverId;
    private $temperature;
    private $weatherCondition;
    private $windSpeed;
    private $humidity;
    private $timestamp;
    
    public function __construct($id = null, $serverId = null, $temperature = 0, $weatherCondition = '', $windSpeed = 0, $humidity = 0, $timestamp = null) {
        $this->id = $id;
        $this->serverId = $serverId;
        $this->temperature = $temperature;
        $this->weatherCondition = $weatherCondition;
        $this->windSpeed = $windSpeed;
        $this->humidity = $humidity;
        $this->timestamp = $timestamp;
    }
    
    // Геттеры
    public function getId() { return $this->id; }
    public function getServerId() { return $this->serverId; }
    public function getTemperature() { return $this->temperature; }
    public function getWeatherCondition() { return $this->weatherCondition; }
    public function getWindSpeed() { return $this->windSpeed; }
    public function getHumidity() { return $this->humidity; }
    public function getTimestamp() { return $this->timestamp; }
    
    // Сеттеры
    public function setId($id) { $this->id = $id; }
    public function setServerId($serverId) { $this->serverId = $serverId; }
    public function setTemperature($temperature) { $this->temperature = $temperature; }
    public function setWeatherCondition($weatherCondition) { $this->weatherCondition = $weatherCondition; }
    public function setWindSpeed($windSpeed) { $this->windSpeed = $windSpeed; }
    public function setHumidity($humidity) { $this->humidity = $humidity; }
    public function setTimestamp($timestamp) { $this->timestamp = $timestamp; }
}
?>