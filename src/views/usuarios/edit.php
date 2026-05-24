<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<section style="max-width: 480px; margin: 32px auto 48px auto; background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.10); padding: 36px 32px; font-family: 'Segoe UI', Arial, sans-serif;">
    <form method="post" action="/usuario/editar?id=<?= $usuario['id'] ?>" style="display: flex; flex-direction: column; gap: 18px;">
        <div style="display: flex; flex-direction: column; gap: 6px;">
            <label style="font-weight: 500; color: #333;">Nombre</label>
            <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required style="padding: 12px; border: 1px solid #cfd8dc; border-radius: 8px;">
        </div>
        <div style="display: flex; flex-direction: column; gap: 6px;">
            <label style="font-weight: 500; color: #333;">Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required style="padding: 12px; border: 1px solid #cfd8dc; border-radius: 8px;">
        </div>
        <div style="display: flex; flex-direction: column; gap: 6px;">
            <label style="font-weight: 500; color: #333;">Rol</label>
            <select name="rol" required style="padding: 12px; border: 1px solid #cfd8dc; border-radius: 8px;">
                <option value="enfermero" <?= $usuario['rol'] === 'enfermero' ? 'selected' : '' ?>>Enfermero</option>
                <option value="coordinador" <?= $usuario['rol'] === 'coordinador' ? 'selected' : '' ?>>Coordinador</option>
                <option value="administrador" <?= $usuario['rol'] === 'administrador' ? 'selected' : '' ?>>Administrador</option>
            </select>
        </div>
        <button type="submit" style="width:100%; background:#005b96; color:white; border:none; border-radius:8px; padding:14px; font-weight:700; cursor:pointer;">Actualizar</button>
    </form>
</section>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>