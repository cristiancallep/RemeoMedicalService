<?php 
require_once dirname(dirname(dirname(__DIR__))) . '/config/session.php';
require_once dirname(dirname(dirname(__DIR__))) . '/src/helpers/Auth.php';

// Si el usuario ya está autenticado, redirige al dashboard
if (Auth::isAuthenticated()) {
    header('Location: /dashboard');
    exit;
}

// Inicializar variables
$error = null;
$pdo = null;

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
    $error = 'Error de conexión a la base de datos: ' . htmlspecialchars($e->getMessage());
}

// Procesar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($pdo === null) {
        $error = 'No hay conexión a la base de datos. Por favor, intenta más tarde.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $error = 'Por favor completa todos los campos.';
        } else {
            require_once dirname(dirname(__DIR__)) . '/controllers/AuthController.php';
            $authController = new AuthController($pdo);
            
            $user = $authController->login($email, $password);
            if ($user) {
                header('Location: /dashboard');
                exit;
            } else {
                $error = 'Correo o contraseña incorrectos.';
            }
        }
    }
}

require_once dirname(__DIR__) . '/layouts/header.php'; 
?>

<section>
    <h2>Inicio de sesión</h2>
    <p>Accede con tu correo y contraseña para administrar turnos.</p>

    <?php if ($error): ?>
        <div style="background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['expired'])): ?>
        <div style="background-color: #ffc107; color: #856404; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
            Tu sesión ha expirado. Por favor, inicia sesión nuevamente.
        </div>
    <?php endif; ?>

    <form id="login-form" action="/login" method="post" style="max-width: 400px; margin: 0 auto;">
        <div style="margin-bottom: 15px;">
            <label for="email" style="display: block; margin-bottom: 5px;"><strong>Correo electrónico</strong></label>
            <input type="email" id="email" name="email" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
        </div>
        <div style="margin-bottom: 15px;">
            <label for="password" style="display: block; margin-bottom: 5px;"><strong>Contraseña</strong></label>
            <input type="password" id="password" name="password" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
        </div>
        <button type="submit" style="width: 100%; padding: 10px; background-color: #005b96; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Entrar</button>
    </form>

    <p style="text-align: center; margin-top: 20px; font-size: 12px; color: #666;">
        <strong>Usuarios de prueba:</strong><br>
        Coordinador: coordinador@remeo.local | Password: demo1234<br>
        Administrador: admin@remeo.local | Password: demo1234<br>
        Enfermero: enfermero1@remeo.local | Password: demo1234
    </p>
</section>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
