<?php 
require_once dirname(dirname(__DIR__)) . '/config/session.php';
require_once dirname(dirname(__DIR__)) . '/src/helpers/Auth.php';

if (!Auth::isAuthenticated()) {
    header('Location: /login');
    exit;
}

$user     = Auth::getCurrentUser();
$roleName = Auth::getRoleName($user['rol']);

// Si es enfermero, cargar sus turnos para mostrar en el dashboard
$misTurnos  = [];
$contadores = [];

if (Auth::hasRole('enfermero')) {
    // $pdo viene inyectado desde index.php
    $stmt = $pdo->prepare('
        SELECT t.*, d.direccion AS domicilio_direccion, u.nombre AS coordinador_nombre
        FROM turnos t
        LEFT JOIN domicilios d ON t.domicilio_id = d.id
        LEFT JOIN usuarios u ON t.coordinador_id = u.id
        WHERE t.enfermero_id = :enfermero_id
        ORDER BY t.fecha, t.hora_inicio
    ');
    $stmt->execute(['enfermero_id' => $user['id']]);
    $misTurnos = $stmt->fetchAll();

    $hoy = date('Y-m-d');

    $contadores = [
        'total'      => count($misTurnos),
        'pendiente'  => count(array_filter($misTurnos, fn($t) => $t['estado'] === 'Pendiente')),
        'asignado'   => count(array_filter($misTurnos, fn($t) => $t['estado'] === 'Asignado')),
        'completado' => count(array_filter($misTurnos, fn($t) => $t['estado'] === 'Completado')),
        'cancelado'  => count(array_filter($misTurnos, fn($t) => $t['estado'] === 'Cancelado')),
    ];

    // Próximos 3 turnos (hoy en adelante, no cancelados)
    $proximosTurnos = array_slice(
        array_filter($misTurnos, fn($t) => $t['fecha'] >= $hoy && $t['estado'] !== 'Cancelado'),
        0, 3
    );
}

require_once __DIR__ . '/layouts/header.php';
?>

<style>
    /* ── Variables ── */
    :root {
        --pending:   #f59e0b;
        --assigned:  #10b981;
        --completed: #3b82f6;
        --cancelled: #ef4444;
        --surface:   #ffffff;
        --border:    #e4e7ec;
        --text:      #111827;
        --muted:     #6b7280;
    }

    /* ── Tarjetas de estadística ── */
    .dash-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 12px;
        margin: 16px 0;
    }

    .dash-stat {
        background: #f8f9fb;
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 14px 16px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .dash-stat-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--muted);
    }

    .dash-stat-value {
        font-size: 26px;
        font-weight: 700;
        line-height: 1;
        color: var(--text);
    }

    /* ── Lista de próximos turnos ── */
    .proximos-lista {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 14px;
    }

    .proximo-item {
        display: flex;
        align-items: center;
        gap: 14px;
        background: #f8f9fb;
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 12px 16px;
    }

    .proximo-fecha {
        display: flex;
        flex-direction: column;
        align-items: center;
        min-width: 44px;
        background: #005b96;
        color: white;
        border-radius: 8px;
        padding: 6px 8px;
        line-height: 1;
    }

    .proximo-fecha-dia  { font-size: 20px; font-weight: 700; }
    .proximo-fecha-mes  { font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }

    .proximo-info { flex: 1; }

    .proximo-domicilio {
        font-size: 14px;
        font-weight: 600;
        color: var(--text);
        margin: 0 0 3px;
    }

    .proximo-horario {
        font-size: 12px;
        color: var(--muted);
        margin: 0;
    }

    .proximo-badge {
        font-size: 11px;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 20px;
        color: white;
        white-space: nowrap;
    }

    .sin-turnos {
        text-align: center;
        padding: 24px;
        color: var(--muted);
        font-size: 14px;
        background: #f8f9fb;
        border-radius: 10px;
        border: 1px solid var(--border);
        margin-top: 14px;
    }

    .ver-todos-link {
        display: inline-block;
        margin-top: 14px;
        font-size: 13px;
        color: #005b96;
        font-weight: 600;
        text-decoration: none;
    }

    .ver-todos-link:hover { text-decoration: underline; }
</style>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
    <div>
        <h2>Bienvenido, <?= htmlspecialchars($user['nombre']) ?></h2>
        <p style="color:#666; margin:5px 0;">Rol: <strong><?= htmlspecialchars($roleName) ?></strong></p>
        <p style="color:#666; margin:0;">Email: <strong><?= htmlspecialchars($user['email']) ?></strong></p>
    </div>
    <div>
        <a href="/logout" style="background-color:#dc3545; color:white; padding:10px 15px; text-decoration:none; border-radius:4px; display:inline-block;">Cerrar sesión</a>
    </div>
</div>

<?php if (Auth::hasRole('coordinador')): ?>
    <section style="margin-bottom:30px;">
        <h3>Gestión de turnos</h3>
        <p>Como coordinador, puedes crear, asignar y reasignar turnos.</p>
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px,1fr)); gap:15px; margin-top:15px;">
            <a href="/turnos"        style="background-color:#28a745; color:white; padding:15px; text-align:center; text-decoration:none; border-radius:4px;">Ver turnos</a>
            <a href="/turnos/crear"  style="background-color:#007bff; color:white; padding:15px; text-align:center; text-decoration:none; border-radius:4px;">Crear turno</a>
        </div>
    </section>
<?php endif; ?>

<?php if (Auth::hasRole('administrador')): ?>
    <section style="margin-bottom:30px;">
        <h3>Administración</h3>
        <p>Como administrador, tienes acceso total al sistema.</p>
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px,1fr)); gap:15px; margin-top:15px;">
            <a href="/usuarios"         style="background-color:#6c757d; color:white; padding:15px; text-align:center; text-decoration:none; border-radius:4px;">Gestionar usuarios</a>
            <a href="/turnos"           style="background-color:#28a745; color:white; padding:15px; text-align:center; text-decoration:none; border-radius:4px;">Ver todos los turnos</a>
            <a href="/reglas-laborales" style="background-color:#ffc107; color:black;  padding:15px; text-align:center; text-decoration:none; border-radius:4px;">Reglas laborales</a>
        </div>
    </section>
<?php endif; ?>

<?php if (Auth::hasRole('enfermero')): ?>
    <section style="margin-bottom:30px;">
        <h3>Mis turnos</h3>
        <p style="color:var(--muted); margin:0 0 4px;">Resumen de tu actividad asignada.</p>

        <?php if ($contadores['total'] === 0): ?>
            <div class="sin-turnos">No tenés turnos asignados por el momento.</div>
        <?php else: ?>

            <!-- Contadores -->
            <div class="dash-stats">
                <div class="dash-stat">
                    <span class="dash-stat-label">Total</span>
                    <span class="dash-stat-value"><?= $contadores['total'] ?></span>
                </div>
                <div class="dash-stat">
                    <span class="dash-stat-label">Pendientes</span>
                    <span class="dash-stat-value" style="color:var(--pending)"><?= $contadores['pendiente'] ?></span>
                </div>
                <div class="dash-stat">
                    <span class="dash-stat-label">Asignados</span>
                    <span class="dash-stat-value" style="color:var(--assigned)"><?= $contadores['asignado'] ?></span>
                </div>
                <div class="dash-stat">
                    <span class="dash-stat-label">Completados</span>
                    <span class="dash-stat-value" style="color:var(--completed)"><?= $contadores['completado'] ?></span>
                </div>
                <div class="dash-stat">
                    <span class="dash-stat-label">Cancelados</span>
                    <span class="dash-stat-value" style="color:var(--cancelled)"><?= $contadores['cancelado'] ?></span>
                </div>
            </div>

            <!-- Próximos turnos -->
            <p style="font-size:13px; font-weight:600; color:var(--text); margin:20px 0 0;">Próximos turnos</p>

            <?php if (empty($proximosTurnos)): ?>
                <div class="sin-turnos">No tenés turnos próximos pendientes.</div>
            <?php else: ?>
                <div class="proximos-lista">
                    <?php foreach ($proximosTurnos as $turno):
                        $fechaObj = new DateTime($turno['fecha']);
                        $dia = $fechaObj->format('d');
                        $mes = $fechaObj->format('M');

                        $colorEstado = match($turno['estado']) {
                            'Pendiente'  => '#f59e0b',
                            'Asignado'   => '#10b981',
                            'Completado' => '#3b82f6',
                            default      => '#6b7280'
                        };
                    ?>
                        <div class="proximo-item">
                            <div class="proximo-fecha">
                                <span class="proximo-fecha-dia"><?= $dia ?></span>
                                <span class="proximo-fecha-mes"><?= $mes ?></span>
                            </div>
                            <div class="proximo-info">
                                <p class="proximo-domicilio"><?= htmlspecialchars($turno['domicilio_direccion'] ?? 'Sin domicilio') ?></p>
                                <p class="proximo-horario">
                                    <?= htmlspecialchars($turno['hora_inicio']) ?> — <?= htmlspecialchars($turno['hora_fin']) ?>
                                    &nbsp;·&nbsp; <?= htmlspecialchars($turno['coordinador_nombre'] ?? '-') ?>
                                </p>
                            </div>
                            <span class="proximo-badge" style="background:<?= $colorEstado ?>">
                                <?= htmlspecialchars($turno['estado']) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <a href="/mis-turnos" class="ver-todos-link">Ver todos en el calendario →</a>

        <?php endif; ?>
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