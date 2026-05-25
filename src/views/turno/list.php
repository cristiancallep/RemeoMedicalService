<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<section>
    <!-- MOD: Añadido título y descripción para la vista de listados -->
    <div style="margin-bottom:18px;">
    <a href="javascript:history.back()" class="btn btn-secondary" style="margin-bottom:12px; display:inline-block;">&#8592; Volver</a>
    <div style="display:flex; justify-content:space-between; align-items:center; gap:16px; flex-wrap:wrap;">
        <div>
            <h2>Listado de turnos</h2>
            <p>Gestiona los turnos y asigna enfermeros manualmente desde el coordinador.</p>
        </div>

    <!-- MOD: Mensajes flash mediante parámetros GET (created/updated/deleted) -->
    <?php if (isset($_GET['created'])): ?>
        <div class="alert alert-success">Turno creado correctamente.</div>
    <?php endif; ?>
    <?php if (isset($_GET['updated'])): ?>
        <div class="alert alert-success">Turno actualizado correctamente.</div>
    <?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">Turno eliminado correctamente.</div>
    <?php endif; ?>

    <!-- MOD: Mensaje cuando no hay turnos registrados -->
    <?php if (empty($turnos)): ?>
        <p style="color: #666; text-align: center; padding: 40px;">No hay turnos registrados.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Hora inicio</th>
                    <th>Hora fin</th>
                    <th>Estado</th>
                    <th>Enfermero</th>
                    <th>Domicilio</th>
                    <?php if (Auth::hasRole('coordinador')): ?><th>Acciones</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($turnos as $turno): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($turno['id']); ?></td>
                        <td><?php echo htmlspecialchars($turno['fecha']); ?></td>
                        <td><?php echo htmlspecialchars($turno['hora_inicio']); ?></td>
                        <td><?php echo htmlspecialchars($turno['hora_fin']); ?></td>
                        <td>
                            <?php // MOD: Badge de estado con color dinámico según el valor de 'estado' ?>
                            <?php
                            $estadoColor = match ($turno['estado']) {
                                'Pendiente' => '#ffc107',
                                'Asignado' => '#28a745',
                                'Completado' => '#17a2b8',
                                'Cancelado' => '#dc3545',
                                default => '#6c757d',
                            };
                            ?>
                            <span style="background-color: <?php echo $estadoColor; ?>; color: white; padding: 5px 10px; border-radius: 4px; display:inline-block;">
                                <?php echo htmlspecialchars($turno['estado']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($turno['enfermero_nombre'] ?? 'Sin asignar'); ?></td>
                        <td><?php echo htmlspecialchars($turno['domicilio_direccion'] ?? 'Sin domicilio'); ?></td>
                        <?php if (Auth::hasRole('coordinador')): ?>
                            <!-- MOD: Acciones (Editar / Eliminar) visibles solo para coordinador -->
                            <td style="white-space: nowrap; display: flex; gap: 8px; align-items: center;">
                                <a href="/turnos/editar?id=<?php echo htmlspecialchars($turno['id']); ?>" class="btn btn-success">Editar</a>
                                <form method="post" action="/turnos/eliminar" onsubmit="return confirm('¿Eliminar este turno?');" style="display:inline-flex; margin:0;">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($turno['id']); ?>">
                                    <button type="submit" class="btn btn-danger">Eliminar</button>
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
