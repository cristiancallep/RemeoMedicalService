

<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>


<div class="card-list">
    <div class="card-list-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <a href="/dashboard" class="btn btn-secondary" style="margin-right: 16px;">Volver</a>
            <h2 class="title-blue" style="display: inline-block; vertical-align: middle; margin: 0;">Lista de Reglas Laborales</h2>
        </div>
        <a href="/reglas-laborales/crear" class="btn btn-primary">+ Nueva Regla</a>
    </div>

        <?php if (isset($_GET['success']) && $_GET['success'] === '1'): ?>
            <div class="alert alert-success">Regla laboral creada correctamente.</div>
        <?php endif; ?>
        <?php if (isset($_GET['updated']) && $_GET['updated'] === '1'): ?>
            <div class="alert alert-success">Regla laboral actualizada correctamente.</div>
        <?php endif; ?>
        <?php if (isset($_GET['deactivated']) && $_GET['deactivated'] === '1'): ?>
            <div class="alert alert-warning">Regla laboral desactivada.</div>
        <?php endif; ?>
        <?php if (isset($_GET['activated']) && $_GET['activated'] === '1'): ?>
            <div class="alert alert-warning">Regla laboral activada.</div>
        <?php endif; ?>

        <table class="table-list">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Horas máximas</th>
                    <th>Descanso mínimo</th>
                    <th>Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($reglas as $regla): ?>
                <tr>
                    <td><?= $regla['id'] ?></td>
                    <td><?= htmlspecialchars($regla['horas_maximas']) ?></td>
                    <td><?= htmlspecialchars($regla['descanso_minimo']) ?></td>
                    <td class="capitalize">
                        <?php if ($regla['activo'] == 1): ?>
                            <span class="text-success fw-bold">Activa</span>
                        <?php else: ?>
                            <span class="text-muted">Inactiva</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="actions-row">
                            <form action="/reglas-laborales/editar" method="GET" class="form-inline">
                                <input type="hidden" name="id" value="<?= $regla['id'] ?>">
                                <button type="submit" class="btn btn-edit">Modificar</button>
                            </form>
                            <?php if ($regla['activo'] == 1): ?>
                                <form action="/reglas-laborales/desactivar" method="POST" class="form-inline" onsubmit="return confirm('¿Desactivar esta regla?');">
                                    <input type="hidden" name="id" value="<?= $regla['id'] ?>">
                                    <button type="submit" class="btn btn-warning">Desactivar</button>
                                </form>
                            <?php else: ?>
                                <form action="/reglas-laborales/activar" method="POST" class="form-inline" onsubmit="return confirm('¿Activar esta regla?');">
                                    <input type="hidden" name="id" value="<?= $regla['id'] ?>">
                                    <button type="submit" class="btn btn-primary">Activar</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
