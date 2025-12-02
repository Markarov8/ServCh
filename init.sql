CREATE TABLE IF NOT EXISTS servers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    temperature DECIMAL(5,2) NOT NULL DEFAULT 20.00,
    weather_condition VARCHAR(50) NOT NULL DEFAULT 'sunny',
    wind_speed DECIMAL(4,2) NOT NULL DEFAULT 0.00,
    humidity INT NOT NULL DEFAULT 50,
    timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS weather_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    server_id INT NOT NULL,
    temperature DECIMAL(5,2) NOT NULL,
    weather_condition VARCHAR(50) NOT NULL,
    wind_speed DECIMAL(4,2) NOT NULL,
    humidity INT NOT NULL,
    timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (server_id) REFERENCES servers(id) ON DELETE CASCADE
);

INSERT INTO users (name) VALUES 
('Player_1'),
('Player_2'),
('Player_3');

INSERT INTO servers (name, temperature, weather_condition, wind_speed, humidity) VALUES
('Server_1', 22.5, 'sunny', 3.2, 65),
('Server_2', 18.0, 'cloudy', 1.8, 70),
('Server_3', 25.0, 'rainy', 5.5, 80);