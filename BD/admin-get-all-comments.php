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

$query = "SELECT c.id, c.titulo, c.contenido, c.fecha_comentario, c.pagina, 
                 u.nombre_usuario, u.correo_electronico 
          FROM comentarios c 
          JOIN usuarios u ON c.usuario_id = u.id 
          ORDER BY c.fecha_comentario DESC";

$resultado = $conexion->query($query);

$comentarios = [];
while ($fila = $resultado->fetch_assoc()) {
    $comentarios[] = $fila;
}

echo json_encode(['success' => true, 'comentarios' => $comentarios]);
?>
