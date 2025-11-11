<?php
require_once 'config.php';
header('Content-Type: application/json');

// Solo superadmin puede gestionar roles
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'error' => 'Debes iniciar sesión']);
    exit;
}

$stmt = $conexion->prepare("SELECT rol FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user || $user['rol'] !== 'superadmin') {
    echo json_encode(['success' => false, 'error' => 'Solo superadmins pueden gestionar roles']);
    exit;
}

$usuario_id = $_POST['usuario_id'] ?? 0;
$nuevo_rol = $_POST['nuevo_rol'] ?? '';

$roles_validos = ['usuario', 'admin_moderador', 'admin_verificador', 'superadmin'];
if (!in_array($nuevo_rol, $roles_validos)) {
    echo json_encode(['success' => false, 'error' => 'Rol inválido']);
    exit;
}

$stmt = $conexion->prepare("UPDATE usuarios SET rol = ? WHERE id = ?");
$stmt->bind_param("si", $nuevo_rol, $usuario_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'mensaje' => 'Rol actualizado correctamente']);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al actualizar el rol']);
}
?>
