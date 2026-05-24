<?php 
require_once dirname(dirname(dirname(__DIR__))) . '/config/session.php';
require_once dirname(dirname(dirname(__DIR__))) . '/src/helpers/Auth.php';
require_once dirname(dirname(dirname(__DIR__))) . '/src/controllers/TurnoController.php';

// Si el usuario no está autenticado, redirige a login
if (!Auth::isAuthenticated()) {
    header('Location: /login');
    exit;
}

// Verifica permisos
if (!Auth::hasAnyRole(['coordinador', 'administrador'])) {
    http_response_code(403);
    echo '<h1>Acceso denegado</h1>';
    exit;
}

// Conectar a la base de datos
try {
    $config = [
        'host' => '127.0.0.1',
        'port' => 3306,
        'dbname' => 'remeo_medical_service',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
    ];

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
} catch (PDOException $e) {
    echo '<h1>Error de conexión a la base de datos</h1>';
    exit;
}

$turnoController = new TurnoController($pdo);
$turnos = $turnoController->index();

require_once dirname(__DIR__) . '/layouts/header.php';
?>

<section>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Listado de turnos</h2>
        <?php if (Auth::hasRole('coordinador')): ?>
            <a href="/turnos/crear" style="background-color: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px;">+ Crear turno</a>
        <?php endif; ?>
    </div>

    <?php if (count($turnos) === 0): ?>
        <p style="color: #666; text-align: center; padding: 40px;">No hay turnos registrados.</p>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse; background-color: white;">
            <thead>
                <tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                    <th style="padding: 12px; text-align: left;">ID</th>
                    <th style="padding: 12px; text-align: left;">Fecha</th>
                    <th style="padding: 12px; text-align: left;">Hora inicio</th>
                    <th style="padding: 12px; text-align: left;">Hora fin</th>
                    <th style="padding: 12px; text-align: left;">Estado</th>
                    <th style="padding: 12px; text-align: left;">Enfermero</th>
                    <th style="padding: 12px; text-align: left;">Domicilio</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($turnos as $turno): ?>
                    <tr style="border-bottom: 1px solid #dee2e6;">
                        <td style="padding: 12px;"><?php echo htmlspecialchars($turno['id']); ?></td>
                        <td style="padding: 12px;"><?php echo htmlspecialchars($turno['fecha']); ?></td>
                        <td style="padding: 12px;"><?php echo htmlspecialchars($turno['hora_inicio']); ?></td>
                        <td style="padding: 12px;"><?php echo htmlspecialchars($turno['hora_fin']); ?></td>
                        <td style="padding: 12px;">
                            <?php
                            $estadoColor = match($turno['estado']) {
                                'Pendiente' => '#ffc107',
                                'Asignado' => '#28a745',
                                'Completado' => '#17a2b8',
                                'Cancelado' => '#dc3545',
                                default => '#6c757d'
                            };
                            ?>
                            <span style="background-color: <?php echo $estadoColor; ?>; color: white; padding: 5px 10px; border-radius: 4px;">
                                <?php echo htmlspecialchars($turno['estado']); ?>
                            </span>
                        </td>
                        <td style="padding: 12px;"><?php echo htmlspecialchars($turno['enfermero_nombre'] ?? '-'); ?></td>
                        <td style="padding: 12px;"><?php echo htmlspecialchars($turno['domicilio_direccion'] ?? '-'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
