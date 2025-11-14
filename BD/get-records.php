<?php
require_once 'config.php';
session_start();
header('Content-Type: application/json');

$estado = $_GET['estado'] ?? 'aprobado';
$jefe_filtro = $_GET['jefe'] ?? '';
$categoria_filtro = $_GET['categoria'] ?? '';

$es_verificador = false;
if (isset($_SESSION['usuario_id'])) {
    $stmt_check = $conexion->prepare("SELECT rol FROM usuarios WHERE id = ?");
    $stmt_check->bind_param("i", $_SESSION['usuario_id']);
    $stmt_check->execute();
    $user_data = $stmt_check->get_result()->fetch_assoc();
    $es_verificador = ($user_data['rol'] === 'admin_records' || $user_data['rol'] === 'superadmin');
    $stmt_check->close();
}

$query = "
    SELECT r.id, r.usuario_id, r.jefe_nombre, r.categoria, r.valor, r.descripcion,
           r.imagen_prueba, r.estado, r.verificador_id, r.comentario_verificacion,
           r.fecha_creacion, u.nombre_usuario,
           v.nombre_usuario as verificador_nombre
    FROM records r
    JOIN usuarios u ON r.usuario_id = u.id
    LEFT JOIN usuarios v ON r.verificador_id = v.id
    WHERE 1=1
";

if (!$es_verificador) {
    $query .= " AND r.estado = 'aprobado'";
}

if (!empty($jefe_filtro)) {
    $jefe_filtro = $conexion->real_escape_string($jefe_filtro);
    $query .= " AND r.jefe_nombre = '$jefe_filtro'";
}

if (!empty($categoria_filtro)) {
    $categoria_filtro = $conexion->real_escape_string($categoria_filtro);
    $query .= " AND r.categoria = '$categoria_filtro'";
}

$query .= " ORDER BY r.fecha_creacion DESC LIMIT 100";

$resultado = $conexion->query($query);
$records = [];

if ($resultado) {
    while ($fila = $resultado->fetch_assoc()) {
        $records[] = $fila;
    }
}

echo json_encode(['records' => $records]);
?>
