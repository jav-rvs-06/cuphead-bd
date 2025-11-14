<?php
require_once 'config.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'error' => 'Debes iniciar sesión']);
    exit;
}

$stmt = $conexion->prepare("SELECT rol FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user || !in_array($user['rol'], ['admin_records', 'superadmin'])) {
    echo json_encode(['success' => false, 'error' => 'No tienes permisos para verificar récords']);
    exit;
}

$record_id = $_POST['record_id'] ?? 0;
$accion = $_POST['accion'] ?? '';
$comentario = $_POST['comentario'] ?? '';

if ($record_id <= 0 || !in_array($accion, ['aprobar', 'rechazar'])) {
    echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
    exit;
}

$nuevo_estado = ($accion === 'aprobar') ? 'aprobado' : 'rechazado';
$verificador_id = $_SESSION['usuario_id'];

$stmt = $conexion->prepare("UPDATE records SET verificado = ?, verificador_id = ?, comentario_verificacion = ?, fecha_verificacion = NOW() WHERE id = ?");
$stmt->bind_param("sisi", $nuevo_estado, $verificador_id, $comentario, $record_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'mensaje' => 'Récord ' . $nuevo_estado]);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al actualizar el récord']);
}
$stmt->close();
?>
