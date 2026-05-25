

<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<section class="form-card">
    <div style="margin-bottom: 18px;">
        <a href="/usuarios" class="btn btn-secondary">Volver</a>
    </div>
    <?php if (isset($_GET['success']) && $_GET['success'] === '1'): ?>
        <div class="alert alert-success text-center">Usuario creado correctamente.</div>
    <?php endif; ?>
    <form method="post" action="/usuario/crear" autocomplete="off" class="form-vertical">
        <div class="form-group">
            <label for="nombre" class="label">Nombre</label>
            <input type="text" id="nombre" name="nombre" required autocomplete="off" class="input">
        </div>
        <div class="form-group">
            <label for="email" class="label">Email</label>
            <input type="email" id="email" name="email" required autocomplete="off" class="input">
        </div>
        <div class="form-group">
            <label for="rol" class="label">Rol</label>
            <select id="rol" name="rol" required class="input">
                <option value="enfermero">Enfermero</option>
                <option value="coordinador">Coordinador</option>
                <option value="administrador">Administrador</option>
            </select>
        </div>
        <div class="form-group">
            <label for="password" class="label">Contraseña</label>
            <input type="password" id="password" name="password" required autocomplete="new-password" class="input">
        </div>
        <button type="submit" class="btn btn-primary btn-block">Guardar</button>
    </form>
</section>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
