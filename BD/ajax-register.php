<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once 'config.php';

header('Content-Type: application/json');

if (!isset($conexion)) {
    echo json_encode(['success' => false, 'error' => 'No se pudo establecer conexión con la base de datos']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit();
}

$name = trim($_POST['nombre_usuario'] ?? '');
$email = trim($_POST['correo_electronico'] ?? '');
$password = $_POST['contrasena'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'error' => 'Email y contraseña son obligatorios']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Email no válido']);
    exit();
}

if (strlen($password) < 4) {
    echo json_encode(['success' => false, 'error' => 'La contraseña debe tener al menos 4 caracteres']);
    exit();
}

if (empty($name)) {
    $name = explode('@', $email)[0];
}

$stmt = $conexion->prepare("SELECT id FROM usuarios WHERE correo_electronico = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Error al preparar consulta: ' . $conexion->error]);
    exit();
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'error' => 'Este email ya está registrado']);
    $stmt->close();
    exit();
}
$stmt->close();

$hash_password = password_hash($password, PASSWORD_BCRYPT);
$stmt = $conexion->prepare("INSERT INTO usuarios (nombre_usuario, correo_electronico, contrasena) VALUES (?, ?, ?)");
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Error al preparar inserción: ' . $conexion->error]);
    exit();
}

$stmt->bind_param("sss", $name, $email, $hash_password);

if ($stmt->execute()) {
    $_SESSION['usuario_id'] = $stmt->insert_id;
    $_SESSION['nombre_usuario'] = $name;
    echo json_encode(['success' => true, 'mensaje' => 'Registro exitoso']);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al registrar: ' . $stmt->error]);
}

$stmt->close();
?>
