<?php

require_once dirname(__DIR__) . '/config/session.php';
require_once dirname(__DIR__) . '/src/helpers/Auth.php';

// Conexión a base de datos
$config = require_once dirname(__DIR__) . '/config/database.php';

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
} catch (PDOException $e) {
    http_response_code(500);
    echo '<h1>Error de conexión a la base de datos</h1>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
    exit;
}

// Parsear la ruta
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
$path = '/' . trim(substr($path, strlen($basePath)), '/');

// Rutas públicas (sin autenticación)
$publicRoutes = ['/login', '/logout'];

// Rutas que requieren rol de "administrador"
$adminRoutes = ['/usuarios', '/usuario', '/reglas-laborales',];

// Verificar si la ruta actual está en las rutas de administrador
$isAdminRoute = false;
foreach ($adminRoutes as $adminRoute) {
    if (strpos($path, $adminRoute) === 0) {
        $isAdminRoute = true;
        break;
    }
}

if ($isAdminRoute && !Auth::hasRole('administrador')) {
    http_response_code(403);
    echo '<h1>403 - Acceso denegado</h1><p>No tienes permiso para acceder a esta página.</p>';
    exit;
}

// Si no está autenticado y la ruta no es pública, redirige a login
if (!Auth::isAuthenticated() && !in_array($path, $publicRoutes, true)) {
    header('Location: /login');
    exit;
}

// NOTE: Reutilizamos la conexión creada arriba. Evitamos volver a llamar a
// `require_once` sobre el mismo archivo porque `require_once` devuelve `true`
// en llamadas posteriores, lo que causaba warnings al acceder offsets de array
// sobre un booleano. El objeto `$pdo` ya está disponible.

require_once dirname(__DIR__) . '/src/controllers/TurnoController.php';
require_once dirname(__DIR__) . '/src/models/User.php';
require_once dirname(__DIR__) . '/src/models/Domicilio.php';

// Ruteo ADMIN
switch (true) {
    // Gestión de usuarios (solo admin)
    case $path === '/usuarios' || $path === '/usuarios/':
        //Verifica que esté logeado y que sea admin
        if (Auth::isAuthenticated() && Auth::hasRole('administrador')) {
            require_once dirname(__DIR__) . '/src/controllers/UserController.php';
            $userController = new UserController($pdo);
            $usuarios = $userController->index();
            require_once dirname(__DIR__) . '/src/views/usuarios/list.php';
        } else {
            http_response_code(403);
            echo '<h1>Acceso denegado</h1>';
            echo '<p>No tienes permisos para acceder a esta página.</p>';
        }
        break;



    // Formulario de crear usuario 
    case ($path === '/usuario/crear' || $path === '/usuarios/crear') && $_SERVER['REQUEST_METHOD'] === 'GET':
        $errors = [];
        require_once dirname(__DIR__) . '/src/views/usuarios/create.php';
        break;

    // Crear usuario 
    case ($path === '/usuario/crear' || $path === '/usuarios/crear') && $_SERVER['REQUEST_METHOD'] === 'POST':
        require_once dirname(__DIR__) . '/src/controllers/UserController.php';
        $userController = new UserController($pdo);
        $errors = $userController->store($_POST);
        if ($errors) {
            require_once dirname(__DIR__) . '/src/views/usuarios/create.php';
            break;
        }
        // Redirige con mensaje de éxito
        header('Location: /usuarios?success=1');
        exit;

    // --- EDICIÓN DE USUARIO ---

    case strpos($path, '/usuario/editar') !== false:
        require_once dirname(__DIR__) . '/src/controllers/UserController.php';
        $userController = new UserController($pdo);
        $id = $_GET['id'] ?? $_POST['id'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $userController->update($id, $_POST);
            if ($errors) {
                $usuario = $userController->find($id);
                require_once dirname(__DIR__) . '/src/views/usuarios/edit.php';
                break;
            }
            header('Location: /usuarios?updated=1');
            exit;
        } else {
            $errors = [];
            $usuario = $userController->find($id);
            require_once dirname(__DIR__) . '/src/views/usuarios/edit.php';
        }
        break;

    // Eliminar usuario
    case $path === '/usuario/eliminar' && $_SERVER['REQUEST_METHOD'] === 'POST':
        require_once dirname(__DIR__) . '/src/controllers/UserController.php';
        $userController = new UserController($pdo);
        $userController->delete($_POST['id'] ?? null);
        header('Location: /usuarios?deleted=1');
        exit;
    // Rutas de autenticación
    case $path === '/login':
        require_once dirname(__DIR__) . '/src/views/auth/login.php';
        break;

    case $path === '/logout':
        require_once dirname(__DIR__) . '/src/views/auth/logout.php';
        break;

    // Ruta raíz - redirige a dashboard si está autenticado, a login si no
    case $path === '' || $path === '/':
        if (Auth::isAuthenticated()) {
            header('Location: /dashboard');
        } else {
            header('Location: /login');
        }
        exit;

    // Dashboard
    case $path === '/dashboard':
        if (Auth::isAuthenticated()) {
            require_once dirname(__DIR__) . '/src/views/dashboard.php';
        } else {
            header('Location: /login');
        }
        break;

    // Turnos
    case $path === '/turnos':
        if (Auth::isAuthenticated() && Auth::hasAnyRole(['coordinador', 'administrador'])) {
            $turnoController = new TurnoController($pdo);
            $turnos = $turnoController->index();
            require_once dirname(__DIR__) . '/src/views/turno/list.php';
        } else {
            http_response_code(403);
            echo '<h1>Acceso denegado</h1>';
            echo '<p>No tienes permisos para acceder a esta página.</p>';
        }
        break;

    case $path === '/turnos/crear':
        if (Auth::isAuthenticated() && Auth::hasRole('coordinador')) {
            $turnoController = new TurnoController($pdo);
            $enfermeros = $turnoController->getNurses();
            $domicilios = $turnoController->getDomicilios();
            $errors = [];
            $old = ['estado' => 'Pendiente'];

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $errors = $turnoController->validate($_POST);

                if (empty($errors)) {
                    $turnoController->store($_POST, Auth::getCurrentUser()['id']);
                    header('Location: /turnos?created=1');
                    exit;
                }

                $old = $_POST;
            }

            require_once dirname(__DIR__) . '/src/views/turno/create.php';
        } else {
            http_response_code(403);
            echo '<h1>Acceso denegado</h1>';
            echo '<p>No tienes permisos para acceder a esta página.</p>';
        }
        break;

    case strpos($path, '/turnos/editar') === 0:
        if (Auth::isAuthenticated() && Auth::hasRole('coordinador')) {
            $turnoController = new TurnoController($pdo);
            $enfermeros = $turnoController->getNurses();
            $domicilios = $turnoController->getDomicilios();
            $id = $_GET['id'] ?? $_POST['id'] ?? null;

            if (!$id || !ctype_digit((string)$id)) {
                http_response_code(404);
                echo '<h1>Turno no encontrado</h1>';
                exit;
            }

            $turno = $turnoController->find((int)$id);

            if (!$turno) {
                http_response_code(404);
                echo '<h1>Turno no encontrado</h1>';
                exit;
            }

            $errors = [];

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $errors = $turnoController->validate($_POST);

                if (empty($errors)) {
                    $turnoController->update((int)$id, $_POST);
                    header('Location: /turnos?updated=1');
                    exit;
                }

                $turno = array_merge($turno, $_POST);
            }

            require_once dirname(__DIR__) . '/src/views/turno/edit.php';
        } else {
            http_response_code(403);
            echo '<h1>Acceso denegado</h1>';
            echo '<p>No tienes permisos para acceder a esta página.</p>';
        }
        break;

    case $path === '/turnos/eliminar' && $_SERVER['REQUEST_METHOD'] === 'POST':
        if (Auth::isAuthenticated() && Auth::hasRole('coordinador')) {
            $turnoController = new TurnoController($pdo);
            $turnoController->delete($_POST['id'] ?? null);
            header('Location: /turnos?deleted=1');
            exit;
        } else {
            http_response_code(403);
            echo '<h1>Acceso denegado</h1>';
            echo '<p>No tienes permisos para acceder a esta página.</p>';
        }
        break;

    case $path === '/mis-turnos':
        if (Auth::isAuthenticated() && Auth::hasRole('enfermero')) {
            require_once dirname(__DIR__) . '/src/views/turno/mis_turnos.php';
        } else {
            http_response_code(403);
            echo '<h1>Acceso denegado</h1>';
        }
        break;


    // --- REGLAS LABORALES ---
    case strpos($path, '/reglas-laborales') === 0:
        require_once dirname(__DIR__) . '/src/controllers/ReglaLaboralController.php';
        $reglaController = new ReglaLaboralController($pdo);
        $id = $_GET['id'] ?? $_POST['id'] ?? null;

        // Crear
        if ($path === '/reglas-laborales/crear' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $errors = [];
            require_once dirname(__DIR__) . '/src/views/reglasLaborales/create.php';
            break;
        }
        if ($path === '/reglas-laborales/crear' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $reglaController->store($_POST);
            if ($errors) {
                require_once dirname(__DIR__) . '/src/views/reglasLaborales/create.php';
                break;
            }
            header('Location: /reglas-laborales?success=1');
            exit;
        }

        // Editar
        if ($path === '/reglas-laborales/editar' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $errors = [];
            $regla = $reglaController->getById($id);
            require_once dirname(__DIR__) . '/src/views/reglasLaborales/edit.php';
            break;
        }
        if ($path === '/reglas-laborales/editar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $reglaController->update($id, $_POST);
            $regla = $reglaController->getById($id);
            if ($errors) {
                require_once dirname(__DIR__) . '/src/views/reglasLaborales/edit.php';
                break;
            }
            header('Location: /reglas-laborales?updated=1');
            exit;
        }

        // Desactivar
        if ($path === '/reglas-laborales/desactivar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $reglaController->deactivate($_POST['id']);
            header('Location: /reglas-laborales?deactivated=1');
            exit;
        }

        // Activar
        if ($path === '/reglas-laborales/activar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $reglaController->activate($_POST['id']);
            header('Location: /reglas-laborales?activated=1');
            exit;
        }

        // Listado
        $reglas = $reglaController->index();
        require_once dirname(__DIR__) . '/src/views/reglasLaborales/list.php';
        break;

    // Error 404
    default:
        http_response_code(404);
        require_once dirname(__DIR__) . '/src/views/layouts/header.php';
        echo '<h1>Página no encontrada</h1>';
        echo '<p>La ruta ' . htmlspecialchars($path) . ' no existe.</p>';
        require_once dirname(__DIR__) . '/src/views/layouts/footer.php';
        break;
}

