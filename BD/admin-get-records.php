<?php
require_once 'config.php';
header('Content-Type: application/json');

error_log("[v0] Session check - usuario_id: " . ($_SESSION['usuario_id'] ?? 'not set'));
error_log("[v0] Session check - rol: " . ($_SESSION['rol'] ?? 'not set'));

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['rol'], ['admin_records', 'superadmin'])) {
    echo json_encode(['success' => false, 'error' => 'Acceso denegado.']);
    exit;
}

$estado = $_GET['estado'] ?? 'todos';
error_log("[v0] Requested estado: " . $estado);

try {
    if ($estado === 'todos') {
        $query = "SELECT r.*, u.nombre_usuario 
                  FROM records r 
                  LEFT JOIN usuarios u ON r.usuario_id = u.id 
                  ORDER BY r.fecha_registro DESC";
        $result = $conexion->query($query);
    } else {
        $query = "SELECT r.*, u.nombre_usuario 
                  FROM records r 
                  LEFT JOIN usuarios u ON r.usuario_id = u.id 
                  WHERE r.verificado = ?
                  ORDER BY r.fecha_registro DESC";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("s", $estado);
        $stmt->execute();
        $result = $stmt->get_result();
    }

    if (!$result) {
        error_log("[v0] Query error: " . $conexion->error);
        echo json_encode(['success' => false, 'error' => 'Error al obtener records: ' . $conexion->error]);
        exit;
    }

    $records = [];
    while ($row = $result->fetch_assoc()) {
        $records[] = $row;
    }

    error_log("[v0] Records found: " . count($records));
    error_log("[v0] Query executed: " . $query);

    echo json_encode(['success' => true, 'records' => $records]);

} catch (Exception $e) {
    error_log("[v0] Exception: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Error del servidor: ' . $e->getMessage()]);
}
?>
