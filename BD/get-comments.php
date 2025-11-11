<?php
require_once 'config.php';

header('Content-Type: application/json');

$response = [
    'usuario' => isset($_SESSION['nombre_usuario']) ? $_SESSION['nombre_usuario'] : null,
    'comentarios' => []
];

$pagina_filtro = isset($_GET['pagina']) && !empty($_GET['pagina']) ? $_GET['pagina'] : null;

if ($pagina_filtro) {
    $stmt = $conexion->prepare("
        SELECT c.titulo, c.contenido, c.fecha_comentario, c.pagina, u.nombre_usuario 
        FROM comentarios c 
        JOIN usuarios u ON c.usuario_id = u.id 
        WHERE c.pagina = ?
        ORDER BY c.fecha_comentario DESC 
        LIMIT 50
    ");
    $stmt->bind_param("s", $pagina_filtro);
} else {
    $stmt = $conexion->prepare("
        SELECT c.titulo, c.contenido, c.fecha_comentario, c.pagina, u.nombre_usuario 
        FROM comentarios c 
        JOIN usuarios u ON c.usuario_id = u.id 
        ORDER BY c.fecha_comentario DESC 
        LIMIT 50
    ");
}

$stmt->execute();
$result = $stmt->get_result();

while ($fila = $result->fetch_assoc()) {
    $response['comentarios'][] = $fila;
}

echo json_encode($response);
$stmt->close();
?>
