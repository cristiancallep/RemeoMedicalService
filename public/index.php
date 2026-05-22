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

// Si no está autenticado y la ruta no es pública, redirige a login
if (!Auth::isAuthenticated() && !in_array($path, $publicRoutes, true)) {
    header('Location: /login');
    exit;
}

// Ruteo
switch (true) {
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
            require_once dirname(__DIR__) . '/src/views/turno/list.php';
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

    // Error 404
    default:
        http_response_code(404);
        require_once dirname(__DIR__) . '/src/views/layouts/header.php';
        echo '<h1>Página no encontrada</h1>';
        echo '<p>La ruta ' . htmlspecialchars($path) . ' no existe.</p>';
        require_once dirname(__DIR__) . '/src/views/layouts/footer.php';
        break;
}
