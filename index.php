<?php

session_start();

// Автозагрузка
function autoload($className) {
    $paths = [
        'models/' . $className . '.php',
        'controllers/' . $className . '.php',
        'config/' . $className . '.php'
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
}
spl_autoload_register('autoload');

// Роутинг
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
$request_uri = ltrim($request_uri, '/');
$parts = explode('/', $request_uri);

$controller = 'AdminController';
$action = 'dashboard';
$params = [];
$errorMessage = null;
$notFoundMessage = null;

if (empty($parts[0])) {
    // Проверяем наличие ошибки в GET-параметрах
    if (isset($_GET['error'])) {
        switch ($_GET['error']) {
            case 'temperature':
                $errorMessage = 'Температура должна быть в пределах от -55 до 55.';
                break;
            case 'wind':
                $errorMessage = 'Ветер должен быть в пределах от 0 до 45.';
                break;
            case 'humidity':
                $errorMessage = 'Влажность должна быть в пределах от 0 до 100%.';
                break;
            case 'not_found_user':
                $notFoundMessage = 'Пользователь не найден.';
                break;
            case 'not_found_server':
                $notFoundMessage = 'Сервер не найден.';
                break;
        }
    }
    // Передача ошибок в dashboard
    if ($errorMessage || $notFoundMessage) {
        $params = [$errorMessage, $notFoundMessage];
        $action = 'dashboardWithError';
    }
} 
elseif ($parts[0] === 'server') {
    if (isset($parts[1])) {
        switch ($parts[1]) {
            case 'update':
                $action = 'updateUserWeather';
                break;
            case 'generate':
                $action = 'generateWeather';
                break;
            case 'add':
                $action = 'addServer';
                break;
        }
    }
} 

elseif ($parts[0] === 'user') {
    if (isset($parts[1]) && is_numeric($parts[1])) {
        $params = [(int)$parts[1]];
        $action = 'userPage';
    } 
    elseif (isset($parts[1]) && $parts[1] === 'add') {
        $action = 'addUser';
    }
} 
elseif ($parts[0] === 'log' && isset($parts[1]) && is_numeric($parts[1])) {
    $params = [(int)$parts[1]];
    $action = 'getLog';
}

try {
    $controllerInstance = new $controller();
    if (method_exists($controllerInstance, $action)) {
        if (!empty($params)) {
            call_user_func_array([$controllerInstance, $action], $params);
        } 
        else {
            $controllerInstance->$action();
        }
    } 
    else {
        http_response_code(404);
        echo 'Метод не найден';
    }
} 

catch (Exception $e) {
    error_log('Ошибка: ' . $e->getMessage());
    http_response_code(500);
    echo 'Внутренняя ошибка сервера: ' . htmlspecialchars($e->getMessage());
}
?>