<?php 
require_once dirname(dirname(__DIR__)) . '/config/session.php';
require_once dirname(dirname(__DIR__)) . '/src/helpers/Auth.php';

// Si el usuario no está autenticado, redirige a login
if (!Auth::isAuthenticated()) {
    header('Location: /login');
    exit;
}

$user = Auth::getCurrentUser();
$roleName = Auth::getRoleName($user['rol']);

require_once __DIR__ . '/layouts/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h2>Bienvenido, <?php echo htmlspecialchars($user['nombre']); ?></h2>
        <p style="color: #666; margin: 5px 0;">Rol: <strong><?php echo htmlspecialchars($roleName); ?></strong></p>
        <p style="color: #666; margin: 0;">Email: <strong><?php echo htmlspecialchars($user['email']); ?></strong></p>
    </div>
    <div>
        <a href="/logout" style="background-color: #dc3545; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; display: inline-block;">Cerrar sesión</a>
    </div>
</div>

<?php if (Auth::hasRole('coordinador')): ?>
    <section style="margin-bottom: 30px;">
        <h3>Gestión de turnos</h3>
        <p>Como coordinador, puedes crear, asignar y reasignar turnos.</p>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 15px;">
            <a href="/turnos" style="background-color: #28a745; color: white; padding: 15px; text-align: center; text-decoration: none; border-radius: 4px;">Ver turnos</a>
            <a href="/turnos/crear" style="background-color: #007bff; color: white; padding: 15px; text-align: center; text-decoration: none; border-radius: 4px;">Crear turno</a>
        </div>
    </section>
<?php endif; ?>

<?php if (Auth::hasRole('administrador')): ?>
    <section style="margin-bottom: 30px;">
        <h3>Administración</h3>
        <p>Como administrador, tienes acceso total al sistema.</p>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 15px;">
            <a href="/usuarios" style="background-color: #6c757d; color: white; padding: 15px; text-align: center; text-decoration: none; border-radius: 4px;">Gestionar usuarios</a>
            <a href="/turnos" style="background-color: #28a745; color: white; padding: 15px; text-align: center; text-decoration: none; border-radius: 4px;">Ver todos los turnos</a>
            <a href="/reglas-laborales" style="background-color: #ffc107; color: black; padding: 15px; text-align: center; text-decoration: none; border-radius: 4px;">Reglas laborales</a>
        </div>
    </section>
<?php endif; ?>

<?php if (Auth::hasRole('enfermero')): ?>
    <section style="margin-bottom: 30px;">
        <h3>Mis turnos</h3>
        <p>Visualiza tus turnos asignados.</p>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 15px;">
            <a href="/mis-turnos" style="background-color: #28a745; color: white; padding: 15px; text-align: center; text-decoration: none; border-radius: 4px;">Ver mis turnos</a>
        </div>
    </section>
<?php endif; ?>

<section>
    <h3>Sistema de información</h3>
    <p>Remeo Medical Service es un sistema de gestión de turnos médicos.</p>
    <ul>
        <li><strong>Coordinador:</strong> Crea, asigna y administra turnos.</li>
        <li><strong>Administrador:</strong> Supervisa el sistema completo y gestiona usuarios.</li>
        <li><strong>Enfermero:</strong> Visualiza y gestiona sus turnos asignados.</li>
    </ul>
</section>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
