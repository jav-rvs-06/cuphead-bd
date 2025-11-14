<?php
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'error' => 'Debes iniciar sesión']);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$jefe_nombre = $_POST['jefe_nombre'] ?? '';
$categoria = $_POST['categoria'] ?? '';
$valor = $_POST['valor'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';

if (empty($jefe_nombre) || empty($categoria) || empty($valor)) {
    echo json_encode(['success' => false, 'error' => 'Completa todos los campos obligatorios']);
    exit;
}

if (!isset($_FILES['imagen_prueba']) || $_FILES['imagen_prueba']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'Debes adjuntar una imagen de prueba']);
    exit;
}

$allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
if (!in_array($_FILES['imagen_prueba']['type'], $allowed_types)) {
    echo json_encode(['success' => false, 'error' => 'Solo se permiten imágenes (JPG, PNG, GIF, WEBP)']);
    exit;
}

if ($_FILES['imagen_prueba']['size'] > 5 * 1024 * 1024) {
    echo json_encode(['success' => false, 'error' => 'La imagen no puede superar 5MB']);
    exit;
}

$upload_dir = '../uploads/records/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$extension = pathinfo($_FILES['imagen_prueba']['name'], PATHINFO_EXTENSION);
$nombre_archivo = 'record_' . $usuario_id . '_' . time() . '.' . $extension;
$ruta_completa = $upload_dir . $nombre_archivo;
$ruta_bd = 'uploads/records/' . $nombre_archivo;

if (!move_uploaded_file($_FILES['imagen_prueba']['tmp_name'], $ruta_completa)) {
    echo json_encode(['success' => false, 'error' => 'Error al subir la imagen']);
    exit;
}

$stmt = $conexion->prepare("INSERT INTO records (usuario_id, jefe_nombre, categoria, valor, descripcion, imagen_prueba) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssss", $usuario_id, $jefe_nombre, $categoria, $valor, $descripcion, $ruta_bd);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'mensaje' => 'Récord enviado! Está pendiente de verificación']);
} else {
    unlink($ruta_completa);
    echo json_encode(['success' => false, 'error' => 'Error al guardar el récord']);
}
$stmt->close();
?>
