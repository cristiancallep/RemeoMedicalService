<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<section>
    <div style="display:flex; justify-content:space-between; align-items:center; gap:16px; flex-wrap:wrap; margin-bottom:18px;">
        <div>
            <h2>Editar turno</h2>
            <p>Actualiza los datos del turno y asigna manualmente un enfermero si lo deseas.</p>
        </div>
        <a href="/dashboard" class="btn btn-secondary">Volver</a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul style="margin:0; padding-left: 20px;">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="/turnos/editar?id=<?php echo htmlspecialchars($turno['id']); ?>" autocomplete="off">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($turno['id']); ?>">

        <div>
            <label for="fecha">Fecha</label>
            <input type="date" id="fecha" name="fecha" required value="<?php echo htmlspecialchars($turno['fecha'] ?? ''); ?>">
        </div>

        <div>
            <label for="hora_inicio">Hora inicio</label>
            <input type="time" id="hora_inicio" name="hora_inicio" required value="<?php echo htmlspecialchars($turno['hora_inicio'] ?? ''); ?>">
        </div>

        <div>
            <label for="hora_fin">Hora fin</label>
            <input type="time" id="hora_fin" name="hora_fin" required value="<?php echo htmlspecialchars($turno['hora_fin'] ?? ''); ?>">
        </div>

        <div>
            <label for="domicilio_id">Domicilio</label>
            <select id="domicilio_id" name="domicilio_id">
                <option value="">Selecciona un domicilio</option>
                <?php foreach ($domicilios as $domicilio): ?>
                    <option value="<?php echo htmlspecialchars($domicilio['id']); ?>" <?php echo isset($turno['domicilio_id']) && $turno['domicilio_id'] == $domicilio['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($domicilio['direccion']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="enfermero_id">Enfermero</label>
            <select id="enfermero_id" name="enfermero_id">
                <option value="">Sin asignar</option>
                <?php foreach ($enfermeros as $enfermero): ?>
                    <option value="<?php echo htmlspecialchars($enfermero['id']); ?>" <?php echo isset($turno['enfermero_id']) && $turno['enfermero_id'] == $enfermero['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($enfermero['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="estado">Estado</label>
            <select id="estado" name="estado" required>
                <?php $selectedEstado = $turno['estado'] ?? 'Pendiente'; ?>
                <option value="Pendiente" <?php echo $selectedEstado === 'Pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                <option value="Asignado" <?php echo $selectedEstado === 'Asignado' ? 'selected' : ''; ?>>Asignado</option>
                <option value="Completado" <?php echo $selectedEstado === 'Completado' ? 'selected' : ''; ?>>Completado</option>
                <option value="Cancelado" <?php echo $selectedEstado === 'Cancelado' ? 'selected' : ''; ?>>Cancelado</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar turno</button>
    </form>
</section>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>