<?php
require_once 'config.php';
header('Content-Type: application/json');

// Verificar que el usuario sea superadmin
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'superadmin') {
    echo json_encode(['success' => false, 'error' => 'Solo el superadmin puede asignar roles']);
    exit;
}

// Obtener datos
$usuario_id = $_POST['usuario_id'] ?? '';
$nuevo_rol = $_POST['rol'] ?? '';

// Validar
if (empty($usuario_id) || empty($nuevo_rol)) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

// Validar roles permitidos
$roles_permitidos = ['usuario', 'admin_moderador', 'admin_verificador'];
if (!in_array($nuevo_rol, $roles_permitidos)) {
    echo json_encode(['success' => false, 'error' => 'Rol inválido']);
    exit;
}

// No permitir modificar al superadmin
$stmt = $conexion->prepare("SELECT rol FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

if ($usuario && $usuario['rol'] === 'superadmin') {
    echo json_encode(['success' => false, 'error' => 'No se puede modificar el rol del superadmin']);
    exit;
}

// Actualizar rol
$stmt = $conexion->prepare("UPDATE usuarios SET rol = ? WHERE id = ?");
$stmt->bind_param("si", $nuevo_rol, $usuario_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'mensaje' => 'Rol actualizado correctamente']);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al actualizar el rol']);
}
?>
