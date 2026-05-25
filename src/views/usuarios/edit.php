

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
    <form method="post" action="/usuario/editar?id=<?= $usuario['id'] ?>" class="form-vertical">
        <div class="form-group">
            <label for="nombre" class="label">Nombre</label>
            <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required class="input">
        </div>
        <div class="form-group">
            <label for="email" class="label">Email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required class="input">
        </div>
        <div class="form-group">
            <label for="rol" class="label">Rol</label>
            <select id="rol" name="rol" required class="input">
                <option value="enfermero" <?= $usuario['rol'] === 'enfermero' ? 'selected' : '' ?>>Enfermero</option>
                <option value="coordinador" <?= $usuario['rol'] === 'coordinador' ? 'selected' : '' ?>>Coordinador</option>
                <option value="administrador" <?= $usuario['rol'] === 'administrador' ? 'selected' : '' ?>>Administrador</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Actualizar</button>
    </form>
</section>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>