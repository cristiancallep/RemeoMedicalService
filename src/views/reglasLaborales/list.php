
<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<div style="max-width: 980px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #005b96; margin: 0;">Lista de Reglas Laborales</h2>
        <a href="/reglas-laborales/crear" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px;">+ Nueva Regla</a>
    </div>


    <?php if (isset($_GET['success']) && $_GET['success'] === '1'): ?>
        <div style="background: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 8px; text-align: center;">
            Regla laboral creada correctamente.
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['updated']) && $_GET['updated'] === '1'): ?>
        <div style="background: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 8px; text-align: center;">
            Regla laboral actualizada correctamente.
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['deactivated']) && $_GET['deactivated'] === '1'): ?>
        <div style="background: #fff3cd; color: #856404; padding: 15px; margin-bottom: 20px; border-radius: 8px; text-align: center;">
            Regla laboral desactivada.
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['activated']) && $_GET['activated'] === '1'): ?>
        <div style="background: #fff3cd; color: #856404; padding: 15px; margin-bottom: 20px; border-radius: 8px; text-align: center;">
            Regla laboral activada.
        </div>
    <?php endif; ?>

    <table style="width:100%; border-collapse: collapse;">
        <tr style="background: #f8f9fa; color: #005b96;">
            <th style="padding: 12px; text-align: left;">ID</th>
            <th style="padding: 12px; text-align: left;">Horas máximas</th>
            <th style="padding: 12px; text-align: left;">Descanso mínimo</th>
            <th style="padding: 12px; text-align: left;">Estado</th>
            <th style="padding: 12px; text-align: center;">Acciones</th>
        </tr>
        <?php foreach ($reglas as $regla): ?>
        <tr style="border-bottom: 1px solid #eee;">
            <td style="padding: 12px;"><?= $regla['id'] ?></td>
            <td style="padding: 12px;"><?= htmlspecialchars($regla['horas_maximas']) ?></td>
            <td style="padding: 12px;"><?= htmlspecialchars($regla['descanso_minimo']) ?></td>
            <td style="padding: 12px; text-transform: capitalize;">
                <?php if ($regla['activo'] == 1): ?>
                    <span style="color: #28a745; font-weight: 600;">Activa</span>
                <?php else: ?>
                    <span style="color: #888;">Inactiva</span>
                <?php endif; ?>
            </td>
            <td style="padding: 12px; display: flex; justify-content: center; gap: 8px;">
                <form action="/reglas-laborales/editar" method="GET" style="margin:0;">
                    <input type="hidden" name="id" value="<?= $regla['id'] ?>">
                    <button type="submit" style="background:#005b96; color:white; border:none; padding:6px 12px; border-radius:4px; cursor:pointer; font-size:0.9rem;">
                        Modificar
                    </button>
                </form>
                <?php if ($regla['activo'] == 1): ?>
                    <form action="/reglas-laborales/desactivar" method="POST" onsubmit="return confirm('¿Desactivar esta regla?');">
                        <input type="hidden" name="id" value="<?= $regla['id'] ?>">
                        <button type="submit" style="background:#dc3545; color:white; border:none; padding:6px 12px; border-radius:4px; cursor:pointer; font-size:0.9rem;">Desactivar</button>
                    </form>
                <?php else: ?>
                    <form action="/reglas-laborales/activar" method="POST" onsubmit="return confirm('¿Activar esta regla?');">
                        <input type="hidden" name="id" value="<?= $regla['id'] ?>">
                        <button type="submit" style="background: #f9e7ab; color: #856404; border:none; padding:6px 12px; border-radius:4px; cursor:pointer; font-size:0.9rem; font-weight:600;">Activar</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
