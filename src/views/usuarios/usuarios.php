<?php
require_once dirname(dirname(dirname(__DIR__))) . '/config/session.php';
require_once dirname(dirname(dirname(__DIR__))) . '/src/helpers/Auth.php';
require_once dirname(dirname(dirname(__DIR__))) . '/src/controllers/UserController.php';

// Solo administradores pueden acceder (Si no se ha loggeado o no es administrador, lo redirige al login)
if (!Auth::isAuthenticated() || !Auth::hasRole('administrador')) {
    header('Location: /login');
    exit;
}

// Trae $pdo de la conexión a la base de datos
global $pdo;
$userController = new UserController($pdo);
//Llama al index del controlador que Llama al método getAll() del modelo User
$usuarios = $userController->index();

//Cargar la vista de la barra de navegación y estilos css
require_once dirname(__DIR__) . '/layouts/header.php';
?>

<div style="max-width: 980px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #005b96; margin: 0;">Lista de Usuarios</h2>
        <a href="/usuario/crear" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px;">+ Crear usuario</a>
    </div>

    <?php if (isset($_GET['updated']) && $_GET['updated'] === '1'): ?>
        <div style="background: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 8px; text-align: center;">
            Usuario actualizado correctamente.
        </div>
    <?php endif; ?>
    
    <table style="width:100%; border-collapse: collapse;">
        <tr style="background: #f8f9fa; color: #005b96;">
            <th style="padding: 12px; text-align: left;">ID</th>
            <th style="padding: 12px; text-align: left;">Nombre</th>
            <th style="padding: 12px; text-align: left;">Email</th>
            <th style="padding: 12px; text-align: left;">Rol</th>
            <th style="padding: 12px; text-align: center;">Acciones</th>
        </tr>
        <?php foreach ($usuarios as $u): ?>
        <tr style="border-bottom: 1px solid #eee;">
            <td style="padding: 12px;"><?= $u['id'] ?></td>
            <td style="padding: 12px;"><?= htmlspecialchars($u['nombre']) ?></td>
            <td style="padding: 12px;"><?= htmlspecialchars($u['email']) ?></td>
            <td style="padding: 12px; text-transform: capitalize;"><?= $u['rol'] ?></td>
            <td style="padding: 12px; display: flex; justify-content: center; gap: 8px;">
                 <form action="/usuario/editar" method="GET" style="margin:0;">
                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                    <button type="submit" style="background:#005b96; color:white; border:none; padding:6px 12px; border-radius:4px; cursor:pointer; font-size:0.9rem;">
                        Modificar
                    </button>
                </form>
                <form action="/usuario/eliminar" method="post" onsubmit="return confirm('¿Seguro?');">
                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                    <button type="submit" style="background:#dc3545; color:white; border:none; padding:6px 12px; border-radius:4px; cursor:pointer; font-size:0.9rem;">Eliminar</button>
                </form>
               
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
