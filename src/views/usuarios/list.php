<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>
<h2>Usuarios</h2>
<a href="/usuarios/crear" class="btn btn-primary">Nuevo Usuario</a>
<table>
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
                <a href="/usuarios/editar?id=<?= $usuario['id'] ?>">Editar</a>
                <a href="/usuarios/eliminar?id=<?= $usuario['id'] }" data-confirm="¿Eliminar usuario?">Eliminar</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
