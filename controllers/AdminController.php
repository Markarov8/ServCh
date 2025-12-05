<?php

class AdminController {
    
    public function dashboard($errorMessage = null, $notFoundMessage = null) { // Принимаем сообщения
        require_once __DIR__ . '/../config/database.php';
        require_once __DIR__ . '/../models/Server.php';
        require_once __DIR__ . '/../models/User.php';
        
        $db = Database::getInstance()->getConnection();
        
        // Получаем серверы
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
        
        // Получаем игроков
        $stmt = $db->query("SELECT * FROM users ORDER BY id");
        $users = [];
        while ($row = $stmt->fetch()) {
            $users[] = new User($row['id'], $row['name']);
        }

        $error_message = $errorMessage;
        $not_found_message = $notFoundMessage;
        
        include __DIR__ . '/../views/dashboard.php';
    }
    
    public function dashboardWithError($errorMessage, $notFoundMessage) {
        $this->dashboard($errorMessage, $notFoundMessage);
    }
    
    public function updateUserWeather() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $serverId = (int)($_POST['server_id'] ?? 0);
            $temp = (float)($_POST['temperature'] ?? 0);
            $weather_condition = trim($_POST['weather_condition'] ?? '');
            $wind = (float)($_POST['wind'] ?? 0);
            $humidity = (int)($_POST['humidity'] ?? 0);
            
            // Проверка температуры
            if (!is_numeric($_POST['temperature']) || $temp < -55 || $temp > 55) {
                header('Location: /?error=temperature');
                exit;
            }
            
            // Проверка ветра
            if (!is_numeric($_POST['wind']) || $wind < 0 || $wind > 45) {
                header('Location: /?error=wind');
                exit;
            }
            
            // Проверка влажности
            if ($humidity < 0 || $humidity > 100) {
                header('Location: /?error=humidity');
                exit;
            }
            
            if ($serverId > 0) {
                require_once __DIR__ . '/../config/database.php';
                $db = Database::getInstance()->getConnection();
                
                // Обновляем текущую погоду
                $stmt = $db->prepare("
                    UPDATE servers 
                    SET temperature = ?, weather_condition = ?, wind_speed = ?, humidity = ?, timestamp = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$temp, $weather_condition, $wind, $humidity, $serverId]);
                
                // Логируем изменение
                $stmt = $db->prepare("
                    INSERT INTO weather_logs (server_id, temperature, weather_condition, wind_speed, humidity, timestamp)
                    VALUES (?, ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([$serverId, $temp, $weather_condition, $wind, $humidity]);
            }
            header('Location: /');
            exit;
        }
    }
    
    public function generateWeather() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $serverId = (int)($_POST['server_id'] ?? 0);
            
            if ($serverId > 0) {
                require_once __DIR__ . '/../config/database.php';
                $db = Database::getInstance()->getConnection();
                
                // Получаем текущую погоду
                $stmt = $db->prepare("SELECT * FROM servers WHERE id = ?");
                $stmt->execute([$serverId]);
                $current = $stmt->fetch();
                
                if ($current) {
                    // Генерация значений
                    $newTemp = max(-55, min(55, $current['temperature'] + (mt_rand(-30, 30) / 10)));
                    $newWind = max(0, min(45, $current['wind_speed'] + (mt_rand(-20, 20) / 10)));
                    $newHumidity = max(0, min(100, $current['humidity'] + mt_rand(-10, 10)));
                    if ($newTemp < -2){
                        $conditions = ['sunny', 'cloudy', 'snowstormy', 'snowy', 'foggy'];
                        $newCondition = $conditions[array_rand($conditions)];
                    }
                    else{
                        $conditions = ['sunny', 'cloudy', 'rainy', 'stormy', 'foggy'];
                        $newCondition = $conditions[array_rand($conditions)];
                    }
                    
                    // Обновляем погоду
                    $stmt = $db->prepare("
                        UPDATE servers 
                        SET temperature = ?, weather_condition = ?, wind_speed = ?, humidity = ?, timestamp = NOW()
                        WHERE id = ?
                    ");
                    $stmt->execute([$newTemp, $newCondition, $newWind, $newHumidity, $serverId]);
                    
                    // Логируем изменение
                    $stmt = $db->prepare("
                        INSERT INTO weather_logs (server_id, temperature, weather_condition, wind_speed, humidity, timestamp)
                        VALUES (?, ?, ?, ?, ?, NOW())
                    ");
                    $stmt->execute([$serverId, $newTemp, $newCondition, $newWind, $newHumidity]);
                }
            }
            header('Location: /');
            exit;
        }
    }
    
    public function addServer() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            
            if ($name) {
                require_once __DIR__ . '/../config/database.php';
                $db = Database::getInstance()->getConnection();
                
                // Генерация случайных начальных данных
                $temp = mt_rand(5, 25);
                $conditions = ['sunny', 'cloudy', 'rainy'];
                $condition = $conditions[array_rand($conditions)];
                $wind = mt_rand(0, 34) / 2;
                $humidity = mt_rand(40, 90);
                
                $stmt = $db->prepare("
                    INSERT INTO servers (name, temperature, weather_condition, wind_speed, humidity, timestamp)
                    VALUES (?, ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([$name, $temp, $condition, $wind, $humidity]);
            }
            header('Location: /');
            exit;
        }
    }
    
    public function userPage($userId) {
        require_once __DIR__ . '/../config/database.php';
        require_once __DIR__ . '/../models/User.php';
        require_once __DIR__ . '/../models/Server.php';
        
        $db = Database::getInstance()->getConnection();
        
        // Получаем игрока
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if (!$user) {
            header('Location: /?error=not_found_user');
            exit;
        }
        
        $userObj = new User($user['id'], $user['name']);
        
        // Получаем серверы
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
        
        include __DIR__ . '/../views/user.php';
    }
    
    public function addUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            
            if ($name) {
                require_once __DIR__ . '/../config/database.php';
                $db = Database::getInstance()->getConnection();
                
                $stmt = $db->prepare("INSERT INTO users (name) VALUES (?)");
                $stmt->execute([$name]);
            }
            
            header('Location: /');
            exit;
        }
    }
    
    public function getLog($serverId) {
        require_once __DIR__ . '/../config/database.php';
        require_once __DIR__ . '/../models/WeatherLog.php';
        
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("SELECT id FROM servers WHERE id = ?");
        $stmt->execute([$serverId]);
        if (!$stmt->fetch()) {
            header('Location: /?error=not_found_server');
            exit;
        }

        $stmt = $db->prepare("
            SELECT * FROM weather_logs 
            WHERE server_id = ? 
            ORDER BY timestamp DESC 
            LIMIT 50
        ");
        $stmt->execute([$serverId]);
        $logs = [];
        while ($row = $stmt->fetch()) {
            $logs[] = new WeatherLog(
                $row['id'],
                $row['server_id'],
                $row['temperature'],
                $row['weather_condition'],
                $row['wind_speed'],
                $row['humidity'],
                $row['timestamp']
            );
        }
        
        
        include __DIR__ . '/../views/log.php';
    }
}
?>