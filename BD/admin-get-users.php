<?php
require_once 'config.php';
session_start();
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

if (!$usuario || !in_array($usuario['rol'], ['admin_comunidad', 'superadmin'])) {
    echo json_encode(['success' => false, 'error' => 'No tienes permisos de administrador']);
    exit;
}

$query = "SELECT id, nombre_usuario, correo_electronico, rol, activo, fecha_creacion as fecha_registro
          FROM usuarios 
          ORDER BY fecha_creacion DESC";
$resultado = $conexion->query($query);

$usuarios = [];
while ($fila = $resultado->fetch_assoc()) {
    $usuarios[] = $fila;
}

echo json_encode(['success' => true, 'usuarios' => $usuarios]);
?>
