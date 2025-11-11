<?php
require_once 'config.php';
header('Content-Type: application/json');

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'error' => 'Debes iniciar sesión']);
    exit;
}

// Verificar que la cuenta esté activa
$stmt = $conexion->prepare("SELECT activo FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || $user['activo'] != 1) {
    echo json_encode(['success' => false, 'error' => 'Tu cuenta está deshabilitada']);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$jefe_nombre = $_POST['jefe_nombre'] ?? '';
$categoria = $_POST['categoria'] ?? '';
$valor = $_POST['valor'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';

// Validaciones
if (empty($jefe_nombre) || empty($categoria) || empty($valor)) {
    echo json_encode(['success' => false, 'error' => 'Completa todos los campos obligatorios']);
    exit;
}

// Validar que se subió una imagen
if (!isset($_FILES['imagen_prueba']) || $_FILES['imagen_prueba']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'Debes adjuntar una imagen de prueba']);
    exit;
}

// Validar tipo de archivo
$allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
$file_type = $_FILES['imagen_prueba']['type'];
if (!in_array($file_type, $allowed_types)) {
    echo json_encode(['success' => false, 'error' => 'Solo se permiten imágenes (JPG, PNG, GIF, WEBP)']);
    exit;
}

// Validar tamaño (máximo 5MB)
if ($_FILES['imagen_prueba']['size'] > 5 * 1024 * 1024) {
    echo json_encode(['success' => false, 'error' => 'La imagen no puede superar 5MB']);
    exit;
}

// Crear carpeta de uploads si no existe
$upload_dir = '../uploads/records/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Generar nombre único para la imagen
$extension = pathinfo($_FILES['imagen_prueba']['name'], PATHINFO_EXTENSION);
$nombre_archivo = 'record_' . $usuario_id . '_' . time() . '.' . $extension;
$ruta_completa = $upload_dir . $nombre_archivo;
$ruta_bd = 'uploads/records/' . $nombre_archivo;

// Mover archivo
if (!move_uploaded_file($_FILES['imagen_prueba']['tmp_name'], $ruta_completa)) {
    echo json_encode(['success' => false, 'error' => 'Error al subir la imagen']);
    exit;
}

// Insertar récord en la base de datos
$stmt = $conexion->prepare("INSERT INTO records (usuario_id, jefe_nombre, categoria, valor, descripcion, imagen_prueba) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssss", $usuario_id, $jefe_nombre, $categoria, $valor, $descripcion, $ruta_bd);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'mensaje' => 'Récord enviado! Está pendiente de verificación']);
} else {
    // Si falla, eliminar la imagen subida
    unlink($ruta_completa);
    echo json_encode(['success' => false, 'error' => 'Error al guardar el récord']);
}
?>
