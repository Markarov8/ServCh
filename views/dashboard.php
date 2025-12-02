<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Панель управления погодой</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8f9fa;
            color: #212529;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 30px;
        }

        .user-selector {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        select, button {
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 0.9rem;
        }

        button {
            background-color: #0d6efd;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #0b5ed7;
        }

        .server-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .weather-info {
            flex: 1;
            padding-right: 20px;
        }

        .weather-info h3 {
            margin-bottom: 10px;
            color: #343a40;
        }

        .weather-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }

        .weather-detail {
            font-size: 0.9rem;
        }

        .weather-detail strong {
            color: #495057;
        }

        .weather-form {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .form-group label {
            font-size: 0.8rem;
            color: #6c757d;
        }

        .form-group input {
            padding: 6px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 0.9rem;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .btn-update {
            background-color: #28a745;
        }

        .btn-generate {
            background-color: #ffc107;
            color: #212529;
        }

        .btn-log {
            background-color: #6c757d;
        }

        .add-server-form {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .add-user-form {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        @media (max-width: 768px) {
            .server-card {
                flex-direction: column;
                align-items: stretch;
            }
            
            .weather-info, .weather-form {
                width: 100%;
                padding-right: 0;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="user-selector">
                <label for="userSelect">Пользователь:</label>
                <select id="userSelect" onchange="location.href='/user/' + this.value;">
                    <option value="">Выберите пользователя</option>
                </select>
                <button onclick="showAddUserForm()">+ Добавить игрока</button>
            </div>
            <button onclick="showAddServerForm()">+ Добавить сервер</button>
        </div>
        
        <div id="addUserForm" class="add-user-form" style="display:none;">
            <h3>Добавить пользователя</h3>
            <form method="post" action="/user/add">
                <div class="form-group">
                    <label for="userName">Имя пользователя:</label>
                    <input type="text" id="userName" name="name" required>
                </div>
                <button type="submit">Добавить</button>
                <button type="button" onclick="hideAddUserForm()">Отмена</button>
            </form>
        </div>
        
        <div id="addServerForm" class="add-server-form" style="display:none;">
            <h3>Добавить сервер</h3>
            <form method="post" action="/server/add">
                <div class="form-group">
                    <label for="serverName">Название сервера:</label>
                    <input type="text" id="serverName" name="name" required>
                </div>
                <button type="submit">Добавить</button>
                <button type="button" onclick="hideAddServerForm()">Отмена</button>
            </form>
        </div>
        
        <?php foreach ($servers as $server): ?>
        <div class="server-card">
            <div class="weather-info">
                <h3><?php echo htmlspecialchars($server->getName()); ?></h3>
                <div class="weather-details">
                    <div class="weather-detail"><strong>Температура:</strong> <?php echo number_format($server->getTemperature(), 1); ?>°C</div>
                    <div class="weather-detail"><strong>Погода:</strong> <?php echo htmlspecialchars($server->getCondition()); ?></div>
                    <div class="weather-detail"><strong>Ветер:</strong> <?php echo number_format($server->getWindSpeed(), 1); ?> м/с</div>
                    <div class="weather-detail"><strong>Влажность:</strong> <?php echo $server->getHumidity(); ?>%</div>
                </div>
            </div>
            
            <div class="weather-form">
                <form method="post" action="/server/update">
                    <input type="hidden" name="server_id" value="<?php echo $server->getId(); ?>">
                    
                    <div class="form-group">
                        <label>Температура (°C):</label>
                        <input type="number" name="temperature" step="0.1" value="<?php echo $server->getTemperature(); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Погода:</label>
                        <input type="text" name="condition" value="<?php echo htmlspecialchars($server->getCondition()); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Ветер (м/с):</label>
                        <input type="number" name="wind" step="0.1" value="<?php echo $server->getWindSpeed(); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Влажность (%):</label>
                        <input type="number" name="humidity" value="<?php echo $server->getHumidity(); ?>" required>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-update">Сохранить</button>
                        <button type="button" class="btn-generate" onclick="generateWeather(<?php echo $server->getId(); ?>)">Случайно</button>
                        <button type="button" class="btn-log" onclick="showLog(<?php echo $server->getId(); ?>)">Лог</button>
                    </div>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <script>
        function showAddUserForm() {
            document.getElementById('addUserForm').style.display = 'block';
        }
        
        function hideAddUserForm() {
            document.getElementById('addUserForm').style.display = 'none';
        }
        
        function showAddServerForm() {
            document.getElementById('addServerForm').style.display = 'block';
        }
        
        function hideAddServerForm() {
            document.getElementById('addServerForm').style.display = 'none';
        }
        
        function generateWeather(serverId) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/server/generate';
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'server_id';
            input.value = serverId;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
        
        function showLog(serverId) {
            fetch('/log/' + serverId)
                .then(response => response.json())
                .then(data => {
                    let logContent = 'Лог изменений для сервера ' + serverId + ':\n\n';
                    data.forEach(log => {
                        logContent += `${log.timestamp} - T:${log.temperature}°C, ${log.condition}, Ветер:${log.wind_speed}м/с, Влажность:${log.humidity}%\n`;
                    });
                    alert(logContent);
                })
                .catch(error => console.error('Ошибка:', error));
        }
    </script>
</body>
</html>