CREATE DATABASE IF NOT EXISTS flashcards_db;
USE flashcards_db;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuario (
  id_usuario INT AUTO_INCREMENT PRIMARY KEY,
  usuario VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(100) NOT NULL UNIQUE,
  contraseña_hash VARCHAR(255) NOT NULL,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de colecciones (grupos de tarjetas)
CREATE TABLE IF NOT EXISTS coleccion (
  id_coleccion INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT NOT NULL,
  nombre VARCHAR(100) NOT NULL,
  categoria VARCHAR(100),
  descripcion TEXT,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  ultimo_acceso TIMESTAMP NULL,
  FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

-- Tabla de tarjetas individuales
CREATE TABLE IF NOT EXISTS tarjeta (
  id_tarjeta INT AUTO_INCREMENT PRIMARY KEY,
  id_coleccion INT NOT NULL,
  termino VARCHAR(150) NOT NULL,
  definicion TEXT NOT NULL,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_coleccion) REFERENCES coleccion(id_coleccion)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);
