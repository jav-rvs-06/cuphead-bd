-- Script para agregar sistema de administración a la base de datos existente
-- Ejecuta este script DESPUÉS de haber ejecutado database.sql

-- Agregar columna 'rol' a la tabla usuarios
ALTER TABLE usuarios 
ADD COLUMN rol ENUM('usuario', 'admin') DEFAULT 'usuario';

-- Agregar columna 'activo' para habilitar/deshabilitar usuarios
ALTER TABLE usuarios 
ADD COLUMN activo TINYINT(1) DEFAULT 1;

-- Crear un usuario administrador (correo: admin@cuphead.com, contraseña: admin123)
-- Hash generado con: password_hash('admin123', PASSWORD_BCRYPT)
INSERT INTO usuarios (nombre_usuario, correo_electronico, contrasena, rol, activo) 
VALUES ('admin', 'admin@cuphead.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1)
ON DUPLICATE KEY UPDATE rol = 'admin';

-- IMPORTANTE: Después de probar, cambia la contraseña del admin
-- Puedes generar un nuevo hash en PHP con: password_hash('tu_contraseña', PASSWORD_BCRYPT)
