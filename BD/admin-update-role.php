<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

$is_superadmin = isset($_SESSION['rol']) && $_SESSION['rol'] === 'superadmin';

if (!$is_superadmin) {
    echo json_encode(['success' => false, 'error' => 'No tienes permiso para cambiar roles. Solo el SuperAdmin puede hacerlo']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit();
}

$usuario_id = intval($_POST['usuario_id'] ?? 0);
$nuevo_rol = trim($_POST['rol'] ?? '');

$roles_validos = ['usuario', 'admin_comentarios', 'admin_records', 'admin_comunidad'];

if ($usuario_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'ID de usuario inválido']);
    exit();
}

if (!in_array($nuevo_rol, $roles_validos)) {
    echo json_encode(['success' => false, 'error' => 'Rol inválido']);
    exit();
}

// Check if user exists and is not superadmin
$check_stmt = $conexion->prepare("SELECT rol FROM usuarios WHERE id = ?");
$check_stmt->bind_param("i", $usuario_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();
$target_user = $check_result->fetch_assoc();

if (!$target_user) {
    echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
    exit();
}

if ($target_user['rol'] === 'superadmin') {
    echo json_encode(['success' => false, 'error' => 'No se puede modificar el rol del superadmin']);
    exit();
}

// Update the role
$stmt = $conexion->prepare("UPDATE usuarios SET rol = ? WHERE id = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Error en la preparación: ' . $conexion->error]);
    exit();
}

$stmt->bind_param("si", $nuevo_rol, $usuario_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'mensaje' => 'Rol actualizado correctamente']);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al actualizar: ' . $stmt->error]);
}

$stmt->close();
?>
