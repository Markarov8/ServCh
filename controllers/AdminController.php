<?php
class AdminController {
    public function dashboard($errorMessage = null, $notFoundMessage = null) {
        // Получаем данные через модели
        $servers = Server::getAll();
        $users = User::getAll();

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

            // Валидация
            if (!is_numeric($_POST['temperature']) || $temp < -55 || $temp > 55) {
                header('Location: /?error=temperature');
                exit;
            }
            if (!is_numeric($_POST['wind']) || $wind < 0 || $wind > 45) {
                header('Location: /?error=wind');
                exit;
            }
            if ($humidity < 0 || $humidity > 100) {
                header('Location: /?error=humidity');
                exit;
            }

            if ($serverId > 0) {
                // Обновляем погоду
                Server::updateWeather($serverId, $temp, $weather_condition, $wind, $humidity);
                // Логируем изменение
                Server::logWeatherChange($serverId, $temp, $weather_condition, $wind, $humidity);
            }
            header('Location: /');
            exit;
        }
    }

    public function generateWeather() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $serverId = (int)($_POST['server_id'] ?? 0);
            if ($serverId > 0) {
                // Генератор погоды
                if (Server::generateRandomWeather($serverId)) {
                    // Логируем
                    $server = Server::findById($serverId);
                    if ($server) {
                        Server::logWeatherChange($serverId, $server->getTemperature(), $server->getWeatherCondition(), $server->getWindSpeed(), $server->getHumidity());
                    }
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
                // Создаем сервер
                $server = new Server(null, $name);
                // Генерация случайных начальных данных
                $server->setTemperature(mt_rand(5, 25));
                $conditions = ['sunny', 'cloudy', 'rainy'];
                $server->setWeatherCondition($conditions[array_rand($conditions)]);
                $server->setWindSpeed(mt_rand(0, 34) / 2);
                $server->setHumidity(mt_rand(40, 90));
                $server->save(); // Сохраняем через модель
            }
            header('Location: /');
            exit;
        }
    }

    public function userPage($userId) {
        // Получаем игрока
        $user = User::findById($userId);
        if (!$user) {
            header('Location: /?error=not_found_user');
            exit;
        }

        // Получаем серверы
        $servers = Server::getAll();

        $userObj = $user;
        include __DIR__ . '/../views/user.php';
    }

    public function addUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            if ($name) {
                // Создаем игрока
                $user = new User(null, $name);
                $user->save();
            }
            header('Location: /');
            exit;
        }
    }

    public function getLog($serverId) {
        // Проверяем существование сервера
        if (!Server::findById($serverId)) {
            header('Location: /?error=not_found_server');
            exit;
        }

        // Получаем логи
        $logs = WeatherLog::getLatestForServer($serverId, 50);

        include __DIR__ . '/../views/log.php';
    }
}
?>