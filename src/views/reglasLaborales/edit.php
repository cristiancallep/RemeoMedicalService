


<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<section class="form-card">
    <div style="margin-bottom: 18px;">
        <a href="/reglas-laborales" class="btn btn-secondary">Volver</a>
    </div>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method="post" action="/reglas-laborales/editar?id=<?= $regla['id'] ?>" class="form-vertical">
        <input type="hidden" name="id" value="<?= $regla['id'] ?>">
        <div class="form-group">
            <label for="horas_maximas" class="label">Horas máximas</label>
            <input type="number" id="horas_maximas" name="horas_maximas" value="<?= htmlspecialchars($regla['horas_maximas']) ?>" required class="input">
        </div>
        <div class="form-group">
            <label for="descanso_minimo" class="label">Descanso mínimo</label>
            <input type="number" id="descanso_minimo" name="descanso_minimo" value="<?= htmlspecialchars($regla['descanso_minimo']) ?>" required class="input">
        </div>
        <button type="submit" class="btn btn-primary btn-block">Actualizar</button>
    </form>
</section>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>