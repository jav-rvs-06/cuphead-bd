<?php
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['is_admin' => false, 'error' => 'No hay sesión activa']);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

$stmt = $conexion->prepare("SELECT rol FROM usuarios WHERE id = ? AND activo = 1");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo json_encode(['is_admin' => false]);
    exit;
}

$usuario = $resultado->fetch_assoc();
$is_admin = ($usuario['rol'] === 'admin');

echo json_encode([
    'is_admin' => $is_admin,
    'rol' => $usuario['rol']
]);
?>
