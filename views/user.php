<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($userObj->getName()); ?>Погода</title>
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

        .back-button {
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 0.9rem;
        }

        .user-name {
            font-size: 1.5rem;
            font-weight: 600;
            color: #343a40;
        }

        .servers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .server-item {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border: 1px solid #e9ecef;
            position: relative;
        }

        .server-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .server-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: #343a40;
        }

        .server-time {
            font-size: 0.9rem;
            color: #6c757d;
        }

        .log-button {
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            cursor: pointer;
            font-size: 0.8rem;
        }

        .weather-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .weather-detail {
            font-size: 0.9rem;
        }

        .weather-detail strong {
            color: #495057;
        }

        @media (max-width: 768px) {
            .servers-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="/" class="back-button">Назад</a>
            <div class="user-name"><?php echo htmlspecialchars($userObj->getName()); ?></div>
        </div>
        
        <div class="servers-grid" id="serversGrid">
            <?php foreach ($servers as $server): ?>
            <div class="server-item" id="server-<?php echo $server->getId(); ?>">
                <div class="server-header">
                    <div>
                        <div class="server-name"><?php echo htmlspecialchars($server->getName()); ?></div>
                        <div class="server-time" id="time-<?php echo $server->getId(); ?>">--:--</div>
                    </div>
                    <a href="/log/<?php echo $server->getId(); ?>" class="log-button" target="_blank">i</a>
                </div>
                <div class="weather-details">
                    <div class="weather-detail"><strong>Температура:</strong> <?php echo number_format($server->getTemperature(), 1); ?>°C</div>
                    <div class="weather-detail"><strong>Погода:</strong> <?php echo htmlspecialchars($server->getWeatherCondition()); ?></div>
                    <div class="weather-detail"><strong>Ветер:</strong> <?php echo number_format($server->getWindSpeed(), 1); ?> м/с</div>
                    <div class="weather-detail"><strong>Влажность:</strong> <?php echo $server->getHumidity(); ?>%</div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        function updateServerTimes() {
            const now = new Date();
            const timeString = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
            
            <?php foreach ($servers as $server): ?>
            document.getElementById('time-<?php echo $server->getId(); ?>').textContent = timeString;
            <?php endforeach; ?>
        }

        // Обновляем время каждую минуту
        setInterval(updateServerTimes, 60000);
        updateServerTimes();

        // Автоматическое обновление данных у игрока
        setInterval(() => {
            fetch(window.location.href)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newGrid = doc.getElementById('serversGrid');
                    if (newGrid) {
                        document.getElementById('serversGrid').innerHTML = newGrid.innerHTML;
                        updateServerTimes();
                    }
                })
                .catch(error => console.error('Ошибка обновления:', error));
        }, 10000);
    </script>
</body>
</html>