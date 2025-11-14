<?php
header('Content-Type: application/json');

require_once 'config.php';

try {
    // Verificar conexión
    if ($conexion->connect_error) {
        echo json_encode([
            'conectado' => false,
            'error' => 'Error de conexión: ' . $conexion->connect_error
        ]);
        exit;
    }

    // Obtener tablas
    $result = $conexion->query("SHOW TABLES FROM cuphead_guia");
    $tables = [];
    
    while ($row = $result->fetch_row()) {
        $tables[] = $row[0];
    }

    $usuariosExiste = in_array('usuarios', $tables);
    $comentariosExiste = in_array('comentarios', $tables);
    $recordsExiste = in_array('records', $tables);

    echo json_encode([
        'conectado' => true,
        'tables' => $tables,
        'tablas_ok' => $usuariosExiste && $comentariosExiste && $recordsExiste,
        'message' => 'Base de datos conectada correctamente'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'conectado' => false,
        'error' => $e->getMessage()
    ]);
}
?>
