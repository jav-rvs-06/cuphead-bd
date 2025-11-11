-- SISTEMA DE RECORDS Y ROLES DE ADMINISTRACIÓN
-- Este script actualiza la base de datos para incluir:
-- 1. Tres tipos de administradores (superadmin, admin_moderador, admin_verificador)
-- 2. Sistema de récords con verificación
-- 3. Superadmin con credenciales específicas

-- Paso 1: Modificar roles de usuarios
ALTER TABLE usuarios MODIFY COLUMN rol ENUM('usuario', 'admin_moderador', 'admin_verificador', 'superadmin') DEFAULT 'usuario';

-- Paso 2: Crear tabla de récords
CREATE TABLE IF NOT EXISTS records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    jefe_nombre VARCHAR(100) NOT NULL,
    categoria ENUM('Tiempo', 'Puntuacion', 'Sin Daño', 'Experto') NOT NULL,
    valor VARCHAR(50) NOT NULL COMMENT 'Tiempo en formato MM:SS o puntuación',
    descripcion TEXT,
    imagen_prueba VARCHAR(255) NOT NULL COMMENT 'Ruta de la imagen de prueba',
    estado ENUM('pendiente', 'aprobado', 'rechazado') DEFAULT 'pendiente',
    verificador_id INT NULL COMMENT 'ID del admin que verificó',
    comentario_verificacion TEXT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_verificacion TIMESTAMP NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (verificador_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Paso 3: Crear índices para mejorar rendimiento
CREATE INDEX idx_estado ON records(estado);
CREATE INDEX idx_jefe ON records(jefe_nombre);
CREATE INDEX idx_usuario ON records(usuario_id);

-- Paso 4: Crear carpeta uploads para imágenes de récords
-- NOTA: Debes crear manualmente la carpeta "uploads/records/" en tu proyecto

-- Paso 5: Insertar superadmin con credenciales específicas
-- Contraseña hasheada para "supadmin28"
INSERT INTO usuarios (nombre_usuario, correo_electronico, contrasena, rol, activo) 
VALUES ('SuperAdmin', 'super@admin.com', '$2y$10$Hv8KZQx5yzJxKX.wH7P1..bB4sHW4jvqmjsAjDqMF7fVJHKp3mG8G', 'superadmin', 1)
ON DUPLICATE KEY UPDATE rol = 'superadmin', activo = 1;

-- Listo! Ahora puedes iniciar sesión con:
-- Correo: super@admin.com
-- Contraseña: supadmin28
