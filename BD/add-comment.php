<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'error' => 'Debes iniciar sesión para dejar un comentario']);
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$pagina = trim($_POST['pagina'] ?? '');
$titulo = trim($_POST['titulo'] ?? '');
$contenido = trim($_POST['contenido'] ?? '');

if (empty($pagina) || empty($titulo) || empty($contenido)) {
    echo json_encode(['success' => false, 'error' => 'Todos los campos son obligatorios']);
    exit();
}

if (strlen($titulo) < 3) {
    echo json_encode(['success' => false, 'error' => 'El título debe tener al menos 3 caracteres']);
    exit();
}

if (strlen($contenido) < 10) {
    echo json_encode(['success' => false, 'error' => 'El comentario debe tener al menos 10 caracteres']);
    exit();
}

$stmt = $conexion->prepare("INSERT INTO comentarios (usuario_id, pagina, titulo, contenido) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isss", $usuario_id, $pagina, $titulo, $contenido);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'mensaje' => 'Comentario guardado exitosamente']);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al guardar el comentario']);
}

$stmt->close();
?>
