<?php
/**
 * Script para recargar los datos de prueba en la base de datos
 * Ejecutar: php reload_database.php
 */

$config = [
    'host' => '127.0.0.1',
    'port' => 3306,
    'dbname' => 'remeo_medical_service',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
];

try {
    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s',
        $config['host'],
        $config['port'],
        $config['dbname'],
        $config['charset']
    );

    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo "✓ Conectado a la base de datos.\n\n";

    // Limpiar datos existentes
    echo "Limpiando datos existentes...\n";
    $pdo->exec('DELETE FROM historial WHERE turno_id IN (SELECT id FROM turnos)');
    $pdo->exec('DELETE FROM turnos');
    $pdo->exec('DELETE FROM usuarios');
    $pdo->exec('DELETE FROM domicilios');
    $pdo->exec('DELETE FROM reglas_laborales');
    echo "✓ Datos limpios.\n\n";

    // Hash correcto para "demo1234"
    $passwordHash = '$2y$10$EHMhRd2QT3xBm.bFEYQmUOLYDRc7mz1nL.43IhLEIpuyGcKXWqwr2';

    // Insertar usuarios
    echo "Insertando usuarios...\n";
    $stmt = $pdo->prepare('
        INSERT INTO usuarios (nombre, rol, email, password_hash)
        VALUES (?, ?, ?, ?)
    ');

    $stmt->execute(['Coordinador Principal', 'coordinador', 'coordinador@remeo.local', $passwordHash]);
    $coordinadorId = $pdo->lastInsertId();
    echo "  ✓ Coordinador Principal (ID: $coordinadorId)\n";

    $stmt->execute(['Administrador', 'administrador', 'admin@remeo.local', $passwordHash]);
    $adminId = $pdo->lastInsertId();
    echo "  ✓ Administrador (ID: $adminId)\n";

    $stmt->execute(['Enfermero 001', 'enfermero', 'enfermero1@remeo.local', $passwordHash]);
    $enfermeroPrincipalId = $pdo->lastInsertId();
    echo "  ✓ Enfermero 001 (ID: $enfermeroPrincipalId)\n";

    // Insertar reglas laborales
    echo "\nInsertando reglas laborales...\n";
    $pdo->exec('INSERT INTO reglas_laborales (horas_maximas, descanso_minimo, activo) VALUES (8, 12, 1)');
    echo "  ✓ Regla laboral: 8 horas máximas, 12 horas descanso mínimo\n";

    // Insertar domicilio
    echo "\nInsertando domicilios...\n";
    $domicilioStmt = $pdo->prepare('INSERT INTO domicilios (direccion, ciudad, provincia, codigo_postal, observaciones) VALUES (?, ?, ?, ?, ?)');
    $domicilioStmt->execute(['Calle Falsa 123', 'Ciudad Ejemplo', 'Provincia Demo', '1000', 'Domicilio de prueba']);
    $domicilioId = $pdo->lastInsertId();
    echo "  ✓ Domicilio: Calle Falsa 123 (ID: $domicilioId)\n";

    // Insertar turno
    echo "\nInsertando turnos...\n";
    $turnoStmt = $pdo->prepare('INSERT INTO turnos (fecha, hora_inicio, hora_fin, estado, enfermero_id, domicilio_id, coordinador_id) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $turnoStmt->execute(['2026-06-01', '08:00:00', '14:00:00', 'Pendiente', $enfermeroPrincipalId, $domicilioId, $coordinadorId]);
    $turnoId = $pdo->lastInsertId();
    echo "  ✓ Turno: 2026-06-01 de 08:00 a 14:00 (Enfermero 001, ID: $turnoId)\n";

    // Insertar historial
    echo "\nInsertando historial...\n";
    $historialStmt = $pdo->prepare('INSERT INTO historial (turno_id, usuario_id, cambio) VALUES (?, ?, ?)');
    $historialStmt->execute([$turnoId, $coordinadorId, 'Turno creado y asignado a Enfermero 001']);
    echo "  ✓ Registro de cambio\n";

    echo "\n";
    echo "=========================================\n";
    echo "✓ Base de datos recargada exitosamente\n";
    echo "=========================================\n";
    echo "\nUsuarios de prueba:\n";
    echo "  Email: coordinador@remeo.local       | Contraseña: demo1234 | Rol: Coordinador\n";
    echo "  Email: admin@remeo.local             | Contraseña: demo1234 | Rol: Administrador\n";
    echo "  Email: enfermero1@remeo.local        | Contraseña: demo1234 | Rol: Enfermero\n";
    echo "\n";

} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
