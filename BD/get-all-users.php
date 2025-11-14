<?php
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'superadmin') {
    echo json_encode(['success' => false, 'error' => 'Acceso denegado']);
    exit;
}

// Obtener todos los usuarios excepto el superadmin actual
$stmt = $conexion->prepare("
    SELECT id, nombre_usuario, correo_electronico, rol, activo, fecha_creacion
    FROM usuarios 
    WHERE id != ? 
    ORDER BY 
        CASE rol
            WHEN 'superadmin' THEN 1
            WHEN 'admin_comunidad' THEN 2
            WHEN 'admin_records' THEN 3
            WHEN 'admin_comentarios' THEN 4
            WHEN 'usuario' THEN 5
        END,
        fecha_creacion DESC
");
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$resultado = $stmt->get_result();

$usuarios = [];
while ($usuario = $resultado->fetch_assoc()) {
    $usuario['fecha_registro'] = $usuario['fecha_creacion'];
    $usuarios[] = $usuario;
}

echo json_encode(['success' => true, 'usuarios' => $usuarios]);
$stmt->close();
?>
