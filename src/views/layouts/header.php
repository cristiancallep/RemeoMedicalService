<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<header style="background-color: #005b96; color: white; padding: 20px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div class="container" style="display: flex; align-items: center; gap: 20px;">
        <?php 
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        // El botón solo se muestra si NO estamos en login o dashboard
        if ($path !== '/login' && $path !== '/dashboard' && $path !== '/'): 
        ?>
            <a href="#" onclick="history.back();" style="color: white; text-decoration: none; font-weight: 600; font-size: 0.9rem;">&larr; Atrás</a>
        <?php endif; ?>
        
        <h1 style="margin: 0; font-size: 28px;">Remeo Medical Service</h1>
    </div>
</header>

<main class="container">