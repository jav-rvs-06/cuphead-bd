<?php
require_once 'config.php';
header('Content-Type: application/json');

// Obtener filtros
$estado = $_GET['estado'] ?? 'aprobado'; // Por defecto mostrar solo aprobados
$jefe_filtro = $_GET['jefe'] ?? '';
$categoria_filtro = $_GET['categoria'] ?? '';

// Construir consulta según filtros
$query = "
    SELECT r.*, u.nombre_usuario, v.nombre_usuario as verificador_nombre
    FROM records r
    JOIN usuarios u ON r.usuario_id = u.id
    LEFT JOIN usuarios v ON r.verificador_id = v.id
    WHERE 1=1
";

$params = [];
$types = '';

// Solo mostrar aprobados si el usuario no es admin verificador
if (isset($_SESSION['usuario_id'])) {
    $stmt_check = $conexion->prepare("SELECT rol FROM usuarios WHERE id = ?");
    $stmt_check->bind_param("i", $_SESSION['usuario_id']);
    $stmt_check->execute();
    $user_data = $stmt_check->get_result()->fetch_assoc();
    $es_verificador = ($user_data['rol'] === 'admin_verificador' || $user_data['rol'] === 'superadmin');
} else {
    $es_verificador = false;
}

// Si no es verificador, solo mostrar aprobados
if (!$es_verificador) {
    $query .= " AND r.estado = 'aprobado'";
} else {
    // Si es verificador, puede filtrar por estado
    if (!empty($estado)) {
        $query .= " AND r.estado = ?";
        $params[] = $estado;
        $types .= 's';
    }
}

if (!empty($jefe_filtro)) {
    $query .= " AND r.jefe_nombre = ?";
    $params[] = $jefe_filtro;
    $types .= 's';
}

if (!empty($categoria_filtro)) {
    $query .= " AND r.categoria = ?";
    $params[] = $categoria_filtro;
    $types .= 's';
}

$query .= " ORDER BY r.fecha_creacion DESC LIMIT 100";

$stmt = $conexion->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$resultado = $stmt->get_result();

$records = [];
while ($fila = $resultado->fetch_assoc()) {
    $records[] = $fila;
}

echo json_encode(['records' => $records]);
?>
