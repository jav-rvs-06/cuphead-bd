<?php
header('Content-Type: application/json');
require_once 'config.php';

error_log('[v0] assign-admin-role.php - POST data: ' . json_encode($_POST));
error_log('[v0] Session role: ' . ($_SESSION['rol'] ?? 'NO SESSION'));
error_log('[v0] Session usuario_id: ' . ($_SESSION['usuario_id'] ?? 'NO ID'));

// Verificar que el usuario sea superadmin
if (!isset($_SESSION['usuario_id'])) {
    error_log('[v0] Error: No usuario_id in session');
    echo json_encode(['success' => false, 'error' => 'Sesión no válida - usuario no identificado']);
    exit;
}

if ($_SESSION['rol'] !== 'superadmin') {
    error_log('[v0] Error: User is not superadmin, rol is: ' . $_SESSION['rol']);
    echo json_encode(['success' => false, 'error' => 'Solo el superadmin puede asignar roles']);
    exit;
}

// Obtener datos
$usuario_id = intval($_POST['usuario_id'] ?? 0);
$nuevo_rol = trim($_POST['rol'] ?? '');

error_log('[v0] Attempting to change user ' . $usuario_id . ' to role: ' . $nuevo_rol);

// Validar
if ($usuario_id <= 0 || empty($nuevo_rol)) {
    error_log('[v0] Invalid data - usuario_id: ' . $usuario_id . ', rol: ' . $nuevo_rol);
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

// Validar roles permitidos
$roles_permitidos = ['usuario', 'admin_comunidad', 'admin_records'];
if (!in_array($nuevo_rol, $roles_permitidos)) {
    error_log('[v0] Invalid role: ' . $nuevo_rol);
    echo json_encode(['success' => false, 'error' => 'Rol inválido: ' . $nuevo_rol]);
    exit;
}

// No permitir modificar al superadmin
$stmt = $conexion->prepare("SELECT rol FROM usuarios WHERE id = ?");
if (!$stmt) {
    error_log('[v0] Prepare error: ' . $conexion->error);
    echo json_encode(['success' => false, 'error' => 'Error en la base de datos']);
    exit;
}

$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();
$stmt->close();

if (!$usuario) {
    error_log('[v0] User not found: ' . $usuario_id);
    echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
    exit;
}

if ($usuario['rol'] === 'superadmin') {
    error_log('[v0] Cannot modify superadmin role');
    echo json_encode(['success' => false, 'error' => 'No se puede modificar el rol del superadmin']);
    exit;
}

$update_stmt = $conexion->prepare("UPDATE usuarios SET rol = ? WHERE id = ?");
if (!$update_stmt) {
    error_log('[v0] Update prepare error: ' . $conexion->error);
    echo json_encode(['success' => false, 'error' => 'Error en la preparación de la actualización']);
    exit;
}

$update_stmt->bind_param("si", $nuevo_rol, $usuario_id);

if ($update_stmt->execute()) {
    error_log('[v0] Successfully updated user ' . $usuario_id . ' to role ' . $nuevo_rol);
    echo json_encode(['success' => true, 'mensaje' => 'Rol actualizado correctamente']);
} else {
    error_log('[v0] Execute error: ' . $update_stmt->error);
    echo json_encode(['success' => false, 'error' => 'Error al actualizar el rol: ' . $update_stmt->error]);
}
$update_stmt->close();
?>
