<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

// Verificar que el usuario es admin
$is_admin = isset($_SESSION['rol']) && in_array($_SESSION['rol'], ['superadmin', 'admin_comentarios', 'admin_records']);

if (!$is_admin) {
    echo json_encode(['success' => false, 'error' => 'No tienes permiso']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit();
}

$usuario_id = intval($_POST['usuario_id'] ?? 0);
$nuevo_rol = trim($_POST['nuevo_rol'] ?? '');

$roles_validos = ['usuario', 'admin_comentarios', 'admin_records', 'superadmin'];

if ($usuario_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'ID de usuario inválido']);
    exit();
}

if (!in_array($nuevo_rol, $roles_validos)) {
    echo json_encode(['success' => false, 'error' => 'Rol inválido']);
    exit();
}

$stmt = $conexion->prepare("UPDATE usuarios SET rol = ? WHERE id = ?");
$stmt->bind_param("si", $nuevo_rol, $usuario_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'mensaje' => 'Rol actualizado correctamente']);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al actualizar: ' . $conexion->error]);
}

$stmt->close();
?>
