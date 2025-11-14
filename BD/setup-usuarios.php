<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

if (!isset($conexion)) {
    die('No se pudo establecer conexión con la base de datos');
}

$usuarios = [
    [
        'nombre_usuario' => 'superadmin',
        'correo_electronico' => 'superadmin@cuphead.com',
        'contrasena' => '123456',
        'rol' => 'superadmin',
        'activo' => 1
    ],
    [
        'nombre_usuario' => 'admin_comunidad',
        'correo_electronico' => 'admin_comunidad@cuphead.com',
        'contrasena' => '123456',
        'rol' => 'admin_comunidad',
        'activo' => 1
    ],
    [
        'nombre_usuario' => 'admin_records',
        'correo_electronico' => 'admin_records@cuphead.com',
        'contrasena' => '123456',
        'rol' => 'admin_records',
        'activo' => 1
    ],
    [
        'nombre_usuario' => 'usuario_normal',
        'correo_electronico' => 'usuario@cuphead.com',
        'contrasena' => '123456',
        'rol' => 'usuario',
        'activo' => 1
    ]
];

echo "<h2>Creando Usuarios de Prueba</h2>";
echo "<hr>";

$creados = 0;
$errores = 0;

foreach ($usuarios as $user) {
    $contrasena_hash = password_hash($user['contrasena'], PASSWORD_BCRYPT);
    
    // Verificar si el usuario ya existe
    $stmt_check = $conexion->prepare("SELECT id FROM usuarios WHERE correo_electronico = ?");
    $stmt_check->bind_param("s", $user['correo_electronico']);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    
    if ($result_check->num_rows > 0) {
        echo "<p style='color: orange;'>⚠ Usuario <strong>{$user['nombre_usuario']}</strong> ya existe (se omite)</p>";
        $stmt_check->close();
        continue;
    }
    $stmt_check->close();
    
    // Insertar nuevo usuario
    $stmt = $conexion->prepare("INSERT INTO usuarios (nombre_usuario, correo_electronico, contrasena, rol, activo) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo "<p style='color: red;'>❌ Error al preparar consulta: " . $conexion->error . "</p>";
        $errores++;
        continue;
    }
    
    $stmt->bind_param("ssssi", $user['nombre_usuario'], $user['correo_electronico'], $contrasena_hash, $user['rol'], $user['activo']);
    
    if ($stmt->execute()) {
        echo "<p style='color: green;'>✓ Usuario <strong>{$user['nombre_usuario']}</strong> creado exitosamente</p>";
        echo "  <small>Correo: {$user['correo_electronico']} | Rol: {$user['rol']} | Contraseña: {$user['contrasena']}</small>";
        $creados++;
    } else {
        echo "<p style='color: red;'>❌ Error al crear usuario {$user['nombre_usuario']}: " . $stmt->error . "</p>";
        $errores++;
    }
    
    $stmt->close();
}

echo "<hr>";
echo "<h3>Resumen:</h3>";
echo "<p>✓ Usuarios creados: <strong>{$creados}</strong></p>";
echo "<p>❌ Errores: <strong>{$errores}</strong></p>";
echo "<p><a href='../index.html'>← Volver al Inicio</a></p>";

$conexion->close();
?>
