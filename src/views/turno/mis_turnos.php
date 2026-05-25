<?php
// index.php ya cargó: session.php, Auth.php, y la conexión $pdo
// Solo necesitamos verificar el rol (doble check de seguridad)
if (!Auth::isAuthenticated()) {
    header('Location: /login');
    exit;
}

if (!Auth::hasRole('enfermero')) {
    http_response_code(403);
    echo '<h1>Acceso denegado</h1>';
    exit;
}

$user = Auth::getCurrentUser();

// Obtener solo los turnos del enfermero actual
// $pdo ya viene inyectado desde index.php
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

// Convertir turnos a formato FullCalendar
$eventosCalendario = array_map(fn($turno) => [
    'title'           => $turno['domicilio_direccion'] ?? 'Sin domicilio',
    'start'           => $turno['fecha'] . 'T' . $turno['hora_inicio'],
    'end'             => $turno['fecha'] . 'T' . $turno['hora_fin'],
    'backgroundColor' => match($turno['estado']) {
        'Pendiente'  => '#f59e0b',
        'Asignado'   => '#10b981',
        'Completado' => '#3b82f6',
        'Cancelado'  => '#ef4444',
        default      => '#6b7280'
    },
    'borderColor' => match($turno['estado']) {
        'Pendiente'  => '#d97706',
        'Asignado'   => '#059669',
        'Completado' => '#2563eb',
        'Cancelado'  => '#dc2626',
        default      => '#4b5563'
    },
    'extendedProps' => [
        'estado'      => $turno['estado'],
        'domicilio'   => $turno['domicilio_direccion'] ?? '-',
        'coordinador' => $turno['coordinador_nombre']  ?? '-',
        'hora_inicio' => $turno['hora_inicio'],
        'hora_fin'    => $turno['hora_fin'],
        'fecha'       => $turno['fecha'],
    ]
], $misTurnos);

// Contadores por estado
$contadores = [
    'total'      => count($misTurnos),
    'pendiente'  => count(array_filter($misTurnos, fn($t) => $t['estado'] === 'Pendiente')),
    'asignado'   => count(array_filter($misTurnos, fn($t) => $t['estado'] === 'Asignado')),
    'completado' => count(array_filter($misTurnos, fn($t) => $t['estado'] === 'Completado')),
    'cancelado'  => count(array_filter($misTurnos, fn($t) => $t['estado'] === 'Cancelado')),
];

require_once dirname(__DIR__) . '/layouts/header.php';
?>

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet"/>

<style>
    :root {
        --bg:         #f0f2f5;
        --surface:    #ffffff;
        --surface-2:  #f8f9fb;
        --border:     #e4e7ec;
        --text:       #111827;
        --text-muted: #6b7280;
        --accent:     #005b96; /* mismo azul del header */

        --pending:    #f59e0b;
        --assigned:   #10b981;
        --completed:  #3b82f6;
        --cancelled:  #ef4444;
    }

    .turnos-page {
        font-family: 'DM Sans', sans-serif;
        background: var(--bg);
        min-height: 100vh;
        padding: 32px 24px;
        box-sizing: border-box;
    }

    .page-header {
        margin-bottom: 28px;
    }

    .page-title {
        font-size: 26px;
        font-weight: 600;
        color: var(--text);
        margin: 0;
        letter-spacing: -0.5px;
    }

    .page-subtitle {
        font-size: 14px;
        color: var(--text-muted);
        margin: 4px 0 0;
    }

    /* Tarjetas de resumen */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 14px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 16px 18px;
        display: flex;
        flex-direction: column;
        gap: 6px;
        transition: box-shadow 0.2s;
    }

    .stat-card:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.07);
    }

    .stat-label {
        font-size: 12px;
        font-weight: 500;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-value {
        font-family: 'DM Mono', monospace;
        font-size: 28px;
        font-weight: 500;
        color: var(--text);
        line-height: 1;
    }

    .stat-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-top: 2px;
    }

    /* Calendario */
    .calendar-wrapper {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }

    .fc {
        font-family: 'DM Sans', sans-serif;
        font-size: 14px;
    }

    .fc .fc-toolbar-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--text);
        letter-spacing: -0.3px;
    }

    .fc .fc-button {
        background: var(--surface-2) !important;
        border: 1px solid var(--border) !important;
        color: var(--text) !important;
        font-family: 'DM Sans', sans-serif !important;
        font-size: 13px !important;
        font-weight: 500 !important;
        border-radius: 8px !important;
        padding: 6px 14px !important;
        box-shadow: none !important;
        transition: background 0.15s !important;
    }

    .fc .fc-button:hover {
        background: var(--border) !important;
    }

    .fc .fc-button-active,
    .fc .fc-button-primary:not(:disabled).fc-button-active {
        background: var(--accent) !important;
        border-color: var(--accent) !important;
        color: #fff !important;
    }

    .fc .fc-col-header-cell-cushion {
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted);
        text-decoration: none;
    }

    .fc .fc-daygrid-day-number {
        font-size: 13px;
        color: var(--text-muted);
        text-decoration: none;
        padding: 6px 8px;
    }

    .fc .fc-daygrid-day.fc-day-today {
        background: #eff6ff !important;
    }

    .fc .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
        background: var(--accent);
        color: #fff;
        border-radius: 6px;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
    }

    .fc-event {
        border-radius: 6px !important;
        font-size: 12px !important;
        font-weight: 500 !important;
        padding: 2px 6px !important;
        cursor: pointer !important;
        transition: opacity 0.15s !important;
    }

    .fc-event:hover { opacity: 0.85 !important; }

    /* Contenido personalizado del evento */
    .fc-event-custom {
        padding: 4px 7px;
        display: flex;
        flex-direction: column;
        gap: 2px;
        overflow: hidden;
    }

    .fc-event-custom-hora {
        font-size: 10px;
        font-weight: 700;
        opacity: 0.9;
        white-space: nowrap;
        font-family: 'DM Mono', monospace;
        letter-spacing: 0.2px;
    }

    .fc-event-custom-domicilio {
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.3;
    }

    .fc-event-custom-footer {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 1px;
    }

    .fc-event-custom-duracion {
        font-size: 10px;
        opacity: 0.85;
        font-weight: 500;
    }

    .fc-theme-standard td,
    .fc-theme-standard th {
        border-color: var(--border) !important;
    }

    /* Leyenda */
    .legend {
        display: flex;
        gap: 18px;
        flex-wrap: wrap;
        margin-top: 18px;
        padding-top: 18px;
        border-top: 1px solid var(--border);
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 7px;
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 500;
        font-family: 'DM Sans', sans-serif;
    }

    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 3px;
    }

    /* Estado vacío */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-muted);
    }

    .empty-state-icon { font-size: 48px; margin-bottom: 12px; opacity: 0.4; }
    .empty-state p    { margin: 0; font-size: 15px; }

    /* Modal */
    .modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.35);
        z-index: 9999;
        justify-content: center;
        align-items: center;
        backdrop-filter: blur(2px);
    }

    .modal-backdrop.open { display: flex; }

    .modal {
        background: var(--surface);
        border-radius: 16px;
        width: 100%;
        max-width: 400px;
        padding: 28px;
        position: relative;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        animation: modalIn 0.2s ease;
        font-family: 'DM Sans', sans-serif;
    }

    @keyframes modalIn {
        from { opacity: 0; transform: translateY(12px) scale(0.98); }
        to   { opacity: 1; transform: translateY(0)   scale(1); }
    }

    .modal-close {
        position: absolute;
        top: 16px;
        right: 16px;
        background: var(--surface-2);
        border: 1px solid var(--border);
        border-radius: 8px;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 16px;
        color: var(--text-muted);
        transition: background 0.15s;
    }

    .modal-close:hover { background: var(--border); }

    .modal-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        color: #fff;
        margin-bottom: 16px;
    }

    .modal-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--text);
        margin: 0 0 20px;
        letter-spacing: -0.3px;
    }

    .modal-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid var(--border);
        font-size: 14px;
    }

    .modal-row:last-child { border-bottom: none; }

    .modal-row-label { color: var(--text-muted); font-weight: 500; }

    .modal-row-value {
        color: var(--text);
        font-weight: 500;
        text-align: right;
        max-width: 60%;
    }

    .modal-row-value.mono {
        font-family: 'DM Mono', monospace;
        font-size: 13px;
    }
</style>

<div class="turnos-page">

    <div class="page-header">
        <h2 class="page-title">Mis turnos</h2>
        <p class="page-subtitle">Visualización de tus turnos asignados</p>
    </div>

    <!-- Tarjetas de resumen -->
    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-label">Total</span>
            <span class="stat-value"><?= $contadores['total'] ?></span>
        </div>
        <div class="stat-card">
            <div class="stat-dot" style="background:var(--pending)"></div>
            <span class="stat-label">Pendientes</span>
            <span class="stat-value" style="color:var(--pending)"><?= $contadores['pendiente'] ?></span>
        </div>
        <div class="stat-card">
            <div class="stat-dot" style="background:var(--assigned)"></div>
            <span class="stat-label">Asignados</span>
            <span class="stat-value" style="color:var(--assigned)"><?= $contadores['asignado'] ?></span>
        </div>
        <div class="stat-card">
            <div class="stat-dot" style="background:var(--completed)"></div>
            <span class="stat-label">Completados</span>
            <span class="stat-value" style="color:var(--completed)"><?= $contadores['completado'] ?></span>
        </div>
        <div class="stat-card">
            <div class="stat-dot" style="background:var(--cancelled)"></div>
            <span class="stat-label">Cancelados</span>
            <span class="stat-value" style="color:var(--cancelled)"><?= $contadores['cancelado'] ?></span>
        </div>
    </div>

    <!-- Calendario -->
    <div class="calendar-wrapper">
        <?php if ($contadores['total'] === 0): ?>
            <div class="empty-state">
                <div class="empty-state-icon">📅</div>
                <p>No tenés turnos asignados por el momento.</p>
            </div>
        <?php else: ?>
            <div id="calendario"></div>
            <div class="legend">
                <div class="legend-item">
                    <span class="legend-dot" style="background:var(--pending)"></span> Pendiente
                </div>
                <div class="legend-item">
                    <span class="legend-dot" style="background:var(--assigned)"></span> Asignado
                </div>
                <div class="legend-item">
                    <span class="legend-dot" style="background:var(--completed)"></span> Completado
                </div>
                <div class="legend-item">
                    <span class="legend-dot" style="background:var(--cancelled)"></span> Cancelado
                </div>
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- Modal de detalle -->
<div class="modal-backdrop" id="modal-backdrop">
    <div class="modal">
        <button class="modal-close" id="modal-close">✕</button>
        <span class="modal-badge" id="modal-badge"></span>
        <h3 class="modal-title" id="modal-domicilio"></h3>
        <div class="modal-row">
            <span class="modal-row-label">Fecha</span>
            <span class="modal-row-value mono" id="modal-fecha"></span>
        </div>
        <div class="modal-row">
            <span class="modal-row-label">Hora inicio</span>
            <span class="modal-row-value mono" id="modal-hora-inicio"></span>
        </div>
        <div class="modal-row">
            <span class="modal-row-label">Hora fin</span>
            <span class="modal-row-value mono" id="modal-hora-fin"></span>
        </div>
        <div class="modal-row">
            <span class="modal-row-label">Duración</span>
            <span class="modal-row-value mono" id="modal-duracion"></span>
        </div>
        <div class="modal-row">
            <span class="modal-row-label">Coordinador</span>
            <span class="modal-row-value" id="modal-coordinador"></span>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const eventos = <?= json_encode($eventosCalendario) ?>;

    const coloresEstado = {
        'Pendiente':  '#f59e0b',
        'Asignado':   '#10b981',
        'Completado': '#3b82f6',
        'Cancelado':  '#ef4444',
    };

    const calendar = new FullCalendar.Calendar(document.getElementById('calendario'), {
        initialView: 'dayGridMonth',
        locale:      'es',
        height:      'auto',
        headerToolbar: {
            left:   'prev,next today',
            center: 'title',
            right:  'dayGridMonth,timeGridWeek,listMonth'
        },
        buttonText: {
            today: 'Hoy',
            month: 'Mes',
            week:  'Semana',
            list:  'Lista'
        },
        events: eventos,

        // Personalizar el contenido visual de cada evento
        eventContent: function(arg) {
            const p     = arg.event.extendedProps;
            const start = arg.event.start;
            const end   = arg.event.end;

            // Formatear horas HH:MM
            const fmt = (d) => d
                ? d.toLocaleTimeString('es-AR', { hour: '2-digit', minute: '2-digit', hour12: false })
                : '--:--';

            const horaInicio = fmt(start);
            const horaFin    = fmt(end);

            // Calcular duración
            const horas   = end ? Math.floor((end - start) / (1000 * 60 * 60)) : 0;
            const minutos = end ? Math.floor(((end - start) % (1000 * 60 * 60)) / (1000 * 60)) : 0;
            const duracion = minutos > 0 ? `${horas}h ${minutos}m` : `${horas}h`;

            const html = `
                <div class="fc-event-custom">
                    <div class="fc-event-custom-hora">${horaInicio} — ${horaFin}</div>
                    <div class="fc-event-custom-domicilio">${arg.event.title}</div>
                    <div class="fc-event-custom-footer">
                        <span class="fc-event-custom-duracion">⏱ ${duracion}</span>
                    </div>
                </div>
            `;

            return { html };
        },

        eventClick: function(info) {
            const p = info.event.extendedProps;

            // Calcular duración
            const inicio  = new Date(info.event.start);
            const fin     = new Date(info.event.end);
            const horas   = Math.floor((fin - inicio) / (1000 * 60 * 60));
            const minutos = Math.floor(((fin - inicio) % (1000 * 60 * 60)) / (1000 * 60));
            const duracion = minutos > 0 ? `${horas}h ${minutos}m` : `${horas}h`;

            const badge = document.getElementById('modal-badge');
            badge.textContent      = p.estado;
            badge.style.background = coloresEstado[p.estado] ?? '#6b7280';

            document.getElementById('modal-domicilio').textContent   = p.domicilio;
            document.getElementById('modal-fecha').textContent       = p.fecha;
            document.getElementById('modal-hora-inicio').textContent = p.hora_inicio;
            document.getElementById('modal-hora-fin').textContent    = p.hora_fin;
            document.getElementById('modal-duracion').textContent    = duracion;
            document.getElementById('modal-coordinador').textContent = p.coordinador;

            document.getElementById('modal-backdrop').classList.add('open');
        }
    });

    calendar.render();

    function cerrarModal() {
        document.getElementById('modal-backdrop').classList.remove('open');
    }

    document.getElementById('modal-close').addEventListener('click', cerrarModal);

    document.getElementById('modal-backdrop').addEventListener('click', function(e) {
        if (e.target === this) cerrarModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') cerrarModal();
    });
});
</script>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>