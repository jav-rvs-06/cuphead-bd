<?php
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['rol'], ['admin_records', 'superadmin'])) {
    echo json_encode(['success' => false, 'error' => 'Acceso denegado']);
    exit;
}

$record_id = $_POST['record_id'] ?? '';
$estado = $_POST['estado'] ?? '';

if (empty($record_id) || empty($estado)) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

if (!in_array($estado, ['aprobado', 'rechazado'])) {
    echo json_encode(['success' => false, 'error' => 'Estado inválido']);
    exit;
}

try {
    $stmt = $conexion->prepare("UPDATE records SET verificado = ? WHERE id = ?");
    $stmt->bind_param("si", $estado, $record_id);

    if ($stmt->execute()) {
        $mensaje = $estado === 'aprobado' ? 'Record aprobado correctamente' : 'Record rechazado';
        echo json_encode(['success' => true, 'mensaje' => $mensaje]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al actualizar record: ' . $stmt->error]);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error del servidor: ' . $e->getMessage()]);
}
?>
