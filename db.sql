CREATE DATABASE IF NOT EXISTS login_system;
USE login_system;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  dni VARCHAR(10) NOT NULL,
  nombre VARCHAR(50) NOT NULL,
  apellido VARCHAR(50) NOT NULL,
  fecha_nacimiento DATE NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
  locked TINYINT(1) DEFAULT 0,
  intentosfallidos INT DEFAULT 0,
  reset_token VARCHAR(255) DEFAULT NULL
);

CREATE TABLE access_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  access_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

-- usuario de tipo admin para test. La contraseña es: Mano+1986
INSERT INTO users (dni, nombre, apellido, fecha_nacimiento, email, password, role) 
VALUES ('14276579', 'Diego', 'Maradona', '1960-10-30', 'maradona@dios.com', '$2y$10$jr3x7NnWFvtTj63Hah5WDO96gtnziX5mPPlipeaNOMkxVbVPQFc0m', 'admin');