-- CREACIÓN DE LA BASE DE DATOS
CREATE DATABASE IF NOT EXISTS club_paddle CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci;

-- Acceso a la base de datos
USE club_paddle;

-- Creación de la tabla de usuarios

CREATE TABLE IF NOT EXISTS users_data(
    idUser INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(30) NOT NULL,
    apellidos VARCHAR (30) NOT NULL,
    email VARCHAR(60) UNIQUE NOT NULL,
    telefono VARCHAR(9) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    direccion VARCHAR(50) DEFAULT NULL,
    sexo ENUM('Hombre', 'Mujer') DEFAULT NULL,
    PRIMARY KEY (idUser)
)ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS users_login(
    idLogin INT NOT NULL AUTO_INCREMENT,
    idUser INT NOT NULL UNIQUE,
    usuario VARCHAR (30) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'user') NOT NULL,
    PRIMARY KEY(idLogin),
    CONSTRAINT fk_login_user
        FOREIGN KEY (idUser)
        REFERENCES users_data(idUser)
        ON DELETE CASCADE
        ON UPDATE CASCADE
)ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS citas(
    idCita INT NOT NULL AUTO_INCREMENT,
    idUser INT NOT NULL,
    fecha_cita DATE NOT NULL,
    motivo_cita VARCHAR(255),
    hora_cita TIME NOT NULL,
    PRIMARY KEY (idCita),
    CONSTRAINT fk_citas_user
        FOREIGN KEY (idUser)
        REFERENCES users_data(idUser)
        ON DELETE NO ACTION
        ON UPDATE CASCADE
)ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS noticias (
    idNoticia INT AUTO_INCREMENT NOT NULL,
    idUser INT NOT NULL,
    titulo VARCHAR(200) NOT NULL UNIQUE,
    imagen VARCHAR(255) NOT NULL,
    texto TEXT NOT NULL,
    fecha DATE NOT NULL,
    PRIMARY KEY (idNoticia),
    CONSTRAINT fk_noticias_user
        FOREIGN KEY (idUser)
        REFERENCES users_data(idUser)
        ON DELETE NO ACTION
        ON UPDATE CASCADE
)ENGINE = INNODB;