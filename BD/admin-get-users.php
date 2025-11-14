<?php
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

$stmt = $conexion->prepare("SELECT rol FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();
$stmt->close();

if (!$usuario || !in_array($usuario['rol'], ['admin_comunidad', 'admin_comentarios', 'admin_records', 'superadmin'])) {
    echo json_encode(['success' => false, 'error' => 'No tienes permisos de administrador']);
    exit;
}

if ($usuario['rol'] === 'admin_comunidad') {
    $query = "SELECT id, nombre_usuario, correo_electronico, rol, activo, fecha_creacion
              FROM usuarios 
              WHERE rol IN ('usuario', 'admin_comunidad')
              ORDER BY fecha_creacion DESC";
} else {
    // superadmin ve todos los usuarios
    $query = "SELECT id, nombre_usuario, correo_electronico, rol, activo, fecha_creacion
              FROM usuarios 
              ORDER BY fecha_creacion DESC";
}

$resultado = $conexion->query($query);

$usuarios = [];
while ($fila = $resultado->fetch_assoc()) {
    $fila['fecha_registro'] = $fila['fecha_creacion'];
    $usuarios[] = $fila;
}

echo json_encode(['success' => true, 'usuarios' => $usuarios]);
?>
