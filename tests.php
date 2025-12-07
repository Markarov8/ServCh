<?php

function sendPostRequest($url, $data) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HEADER, true); // Чтобы получить заголовки
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    return [$httpCode, $response];
}

function testValidation() {
    echo "Тестирование серверной части\n";

    $base_url = 'http://localhost:7070';

    // Тест 1: Некорректная температура
    $postData = [
        'server_id' => '1',
        'temperature' => 'not_a_number',
        'weather_condition' => 'sunny',
        'wind' => '5.0',
        'humidity' => '60'
    ];
    [$code, $response] = sendPostRequest("$base_url/server/update", $postData);
    if (strpos($response, 'Location: /?error=temperature') !== false) {
        echo "OK: Некорректная температура отклонена\n";
    } else {
        echo "FAIL: Некорректная температура не отклонена\n";
    }

    // Тест 2: Температура за пределами
    $postData = [
        'server_id' => '1',
        'temperature' => '1000',
        'weather_condition' => 'sunny',
        'wind' => '5.0',
        'humidity' => '60'
    ];
    [$code, $response] = sendPostRequest("$base_url/server/update", $postData);
    if (strpos($response, 'Location: /?error=temperature') !== false) {
        echo "OK: Температура 1000 отклонена\n";
    } else {
        echo "FAIL: Температура 1000 не отклонена\n";
    }

    // Тест 3: Влажность за пределами
    $postData = [
        'server_id' => '1',
        'temperature' => '20.0',
        'weather_condition' => 'sunny',
        'wind' => '5.0',
        'humidity' => '-50'
    ];
    [$code, $response] = sendPostRequest("$base_url/server/update", $postData);
    if (strpos($response, 'Location: /?error=humidity') !== false) {
        echo "OK: Влажность -50 отклонена\n";
    } else {
        echo "FAIL: Влажность -50 не отклонена\n";
    }

    // Тест 4: Доступ к несуществующему серверу
    [$code, $response] = sendPostRequest("$base_url/user/9999", []);
    if ($code == 302 && strpos($response, 'Location: /?error=not_found_user') !== false) {
        echo "OK: Доступ к несуществующему пользователю отклонён\n";
    } else {
        echo "FAIL: Доступ к несуществующему пользователю разрешён\n";
    }

    echo "Тестирование завершено\n";
}

// Запуск тестов
testValidation();
?>