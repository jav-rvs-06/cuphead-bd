<?php
require_once 'config.php';
header('Content-Type: application/json');

// Solo superadmin puede ver lista de admins
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

$stmt = $conexion->prepare("SELECT rol FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user || $user['rol'] !== 'superadmin') {
    echo json_encode(['success' => false, 'error' => 'Solo superadmins']);
    exit;
}

// Obtener todos los usuarios con rol de admin
$stmt = $conexion->prepare("SELECT id, nombre_usuario, correo_electronico, rol, activo FROM usuarios WHERE rol IN ('admin_moderador', 'admin_verificador', 'superadmin') ORDER BY rol, nombre_usuario");
$stmt->execute();
$resultado = $stmt->get_result();

$admins = [];
while ($fila = $resultado->fetch_assoc()) {
    $admins[] = $fila;
}

echo json_encode(['admins' => $admins]);
?>
