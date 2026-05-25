
<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<div class="card-list">
    <div class="card-list-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <a href="/dashboard" class="btn btn-secondary" style="margin-right: 16px;">Volver</a>
            <h2 class="title-blue" style="display: inline-block; vertical-align: middle; margin: 0;">Usuarios</h2>
        </div>
        <a href="/usuarios/crear" class="btn btn-primary">Nuevo Usuario</a>
    </div>
    <?php if (isset($_GET['deleted']) && $_GET['deleted'] === '1'): ?>
        <div class="alert alert-success text-center">Usuario eliminado correctamente.</div>
    <?php endif; ?>
    <table class="table-list">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuario): ?>
            <tr>
                <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                <td><?= htmlspecialchars($usuario['email']) ?></td>
                <td><?= htmlspecialchars($usuario['rol']) ?></td>
                <td>
                    <a href="/usuario/editar?id=<?= $usuario['id'] ?>" class="btn btn-edit">Editar</a>
                    <form action="/usuario/eliminar" method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('¿Eliminar usuario?');">Eliminar</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
