<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Лог изменений</title>
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

        .log-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .log-table th, .log-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        .log-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }

        .log-table tr:last-child td {
            border-bottom: none;
        }

        .log-table tr:hover {
            background-color: #f8f9fa;
        }

        @media (max-width: 768px) {
            .log-table {
                font-size: 0.9rem;
            }
            
            .log-table th, .log-table td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="/" class="back-button">Назад</a>
            <h1>Лог изменений - Server <?php echo $serverId; ?></h1>
        </div>
        
        <table class="log-table">
            <thead>
                <tr>
                    <th>Время</th>
                    <th>Температура</th>
                    <th>Погода</th>
                    <th>Ветер</th>
                    <th>Влажность</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?php echo htmlspecialchars($log->getTimestamp()); ?></td>
                    <td><?php echo number_format($log->getTemperature(), 1); ?>°C</td>
                    <td><?php echo htmlspecialchars($log->getWeatherCondition()); ?></td>
                    <td><?php echo number_format($log->getWindSpeed(), 1); ?> м/с</td>
                    <td><?php echo $log->getHumidity(); ?>%</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>