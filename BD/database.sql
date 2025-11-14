CREATE DATABASE IF NOT EXISTS cuphead_guia;
USE cuphead_guia;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(50) UNIQUE NOT NULL,
    correo_electronico VARCHAR(100) UNIQUE NOT NULL,
    contrasena VARCHAR(255) NOT NULL,
    rol VARCHAR(20) DEFAULT 'usuario' NOT NULL,
    activo TINYINT DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE comentarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    pagina VARCHAR(100) NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    contenido TEXT NOT NULL,
    fecha_comentario TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    jefe_nombre VARCHAR(100) NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    valor VARCHAR(50) NOT NULL,
    descripcion TEXT,
    imagen_prueba VARCHAR(255) NOT NULL,
    -- Changed 'estado' to 'verificado' for consistency across all files
    verificado VARCHAR(20) DEFAULT 'pendiente',
    verificador_id INT,
    comentario_verificacion TEXT,
    -- Changed 'fecha_creacion' to 'fecha_registro' for consistency
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_verificacion TIMESTAMP NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (verificador_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

CREATE INDEX idx_pagina ON comentarios(pagina);
CREATE INDEX idx_usuario ON comentarios(usuario_id);
-- Updated index name to use 'verificado' column
CREATE INDEX idx_verificado ON records(verificado);
CREATE INDEX idx_usuario_records ON records(usuario_id);
