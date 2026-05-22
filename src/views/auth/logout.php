<?php
require_once dirname(dirname(dirname(__DIR__))) . '/config/session.php';
require_once dirname(dirname(dirname(__DIR__))) . '/src/helpers/Auth.php';

// Realiza logout
Auth::logout();

// Redirige a login
header('Location: /login');
exit;
