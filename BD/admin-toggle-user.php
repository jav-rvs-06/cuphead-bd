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

if (!$usuario || !in_array($usuario['rol'], ['superadmin', 'admin_comunidad'])) {
    echo json_encode(['success' => false, 'error' => 'No tienes permisos de administrador']);
    exit;
}

$usuario_id = $_POST['usuario_id'] ?? 0;
$nuevo_estado = $_POST['activo'] ?? 1;

if ($usuario_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'ID de usuario inválido']);
    exit;
}

if ($usuario_id == $_SESSION['usuario_id']) {
    echo json_encode(['success' => false, 'error' => 'No puedes desactivarte a ti mismo']);
    exit;
}

$stmt = $conexion->prepare("SELECT rol FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario_objetivo = $resultado->fetch_assoc();
$stmt->close();

if (!$usuario_objetivo) {
    echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
    exit;
}

if ($usuario['rol'] === 'admin_comunidad') {
    if (!in_array($usuario_objetivo['rol'], ['usuario', 'admin_comunidad'])) {
        echo json_encode(['success' => false, 'error' => 'No tienes permisos para modificar este tipo de usuario']);
        exit;
    }
}

$stmt = $conexion->prepare("UPDATE usuarios SET activo = ? WHERE id = ?");
$stmt->bind_param("ii", $nuevo_estado, $usuario_id);

if ($stmt->execute()) {
    $accion = $nuevo_estado ? 'habilitado' : 'deshabilitado';
    echo json_encode(['success' => true, 'mensaje' => "Usuario $accion correctamente"]);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al actualizar el usuario']);
}
$stmt->close();
?>
