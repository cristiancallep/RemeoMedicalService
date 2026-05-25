


<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<section class="form-card">
    <div style="margin-bottom: 18px;">
        <a href="/reglas-laborales" class="btn btn-secondary">Volver</a>
    </div>
    <?php if (isset($_GET['success']) && $_GET['success'] === '1'): ?>
        <div class="alert alert-success text-center">Regla laboral creada correctamente.</div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method="post" action="/reglas-laborales/crear" autocomplete="off" class="form-vertical">
        <div class="form-group">
            <label for="horas_maximas" class="label">Horas máximas</label>
            <input type="number" id="horas_maximas" name="horas_maximas" required autocomplete="off" class="input">
        </div>
        <div class="form-group">
            <label for="descanso_minimo" class="label">Descanso mínimo</label>
            <input type="number" id="descanso_minimo" name="descanso_minimo" required autocomplete="off" class="input">
        </div>
        <button type="submit" class="btn btn-primary btn-block">Guardar</button>
    </form>
</section>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>