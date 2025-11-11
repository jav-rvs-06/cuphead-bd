-- Solo ejecuta este script si necesitas agregar las columnas manualmente
-- (El archivo crear-admin.php ya lo hace automáticamente)

-- Agregar columna 'rol' a la tabla usuarios
ALTER TABLE usuarios 
ADD COLUMN IF NOT EXISTS rol ENUM('usuario', 'admin') DEFAULT 'usuario';

-- Agregar columna 'activo' para habilitar/deshabilitar usuarios
ALTER TABLE usuarios 
ADD COLUMN IF NOT EXISTS activo TINYINT(1) DEFAULT 1;
