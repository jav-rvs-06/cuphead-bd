<?php
require_once 'config.php';
header('Content-Type: application/json');

// Verificar que el usuario sea superadmin
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'superadmin') {
    echo json_encode(['success' => false, 'error' => 'Acceso denegado']);
    exit;
}

// Obtener todos los usuarios excepto el superadmin actual
$stmt = $conexion->prepare("
    SELECT id, nombre_usuario, correo_electronico, rol, activo, fecha_registro 
    FROM usuarios 
    WHERE id != ? 
    ORDER BY 
        CASE rol
            WHEN 'superadmin' THEN 1
            WHEN 'admin_moderador' THEN 2
            WHEN 'admin_verificador' THEN 3
            WHEN 'usuario' THEN 4
        END,
        fecha_registro DESC
");
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$resultado = $stmt->get_result();

$usuarios = [];
while ($usuario = $resultado->fetch_assoc()) {
    $usuarios[] = $usuario;
}

echo json_encode(['success' => true, 'usuarios' => $usuarios]);
?>
