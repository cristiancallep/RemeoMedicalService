<?php

// Configurar parámetros de sesión ANTES de session_start()
ini_set('session.use_only_cookies', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Cambiar a 1 si usas HTTPS
ini_set('session.cookie_lifetime', 0);
ini_set('session.gc_maxlifetime', 3600);

// Iniciar sesión PHP
session_start();

// Prevenir timeout de sesión
if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > 3600)) {
    session_destroy();
    header('Location: /login?expired=1');
    exit;
}
