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

if (!$usuario || !in_array($usuario['rol'], ['admin_comunidad', 'admin_comentarios', 'superadmin'])) {
    echo json_encode(['success' => false, 'error' => 'No tienes permisos de administrador']);
    exit;
}

$comentario_id = $_POST['comentario_id'] ?? 0;

if ($comentario_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'ID de comentario inválido']);
    exit;
}

$stmt = $conexion->prepare("DELETE FROM comentarios WHERE id = ?");
$stmt->bind_param("i", $comentario_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'mensaje' => 'Comentario eliminado correctamente']);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al eliminar el comentario']);
}
$stmt->close();
?>
