<?php
require_once __DIR__ . '/api.php';

function e($valor): string
{
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

[$codigo, $productos] = api_request('GET', '/productos');

$error = $_GET['err'] ?? null;
if ($codigo !== 200 || !is_array($productos)) {
    $error = is_array($productos) && isset($productos['error'])
        ? $productos['error']
        : 'No se pudo obtener la lista de productos.';
    $productos = [];
}
$mensaje = $_GET['msg'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Minimarket - Productos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Productos del minimarket</h1>

    <?php if ($mensaje): ?><p class="ok"><?= e($mensaje) ?></p><?php endif; ?>
    <?php if ($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?>

    <p><a class="btn" href="form.php">+ Nuevo producto</a></p>

    <table>
        <thead>
            <tr>
                <th>ID</th><th>Nombre</th><th>Proveedor</th>
                <th>Fecha de vencimiento</th><th>Precio</th><th>Stock</th><th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($productos)): ?>
            <tr><td colspan="7">No hay productos activos.</td></tr>
        <?php endif; ?>
        <?php foreach ($productos as $p): ?>
            <tr>
                <td><?= e($p['id']) ?></td>
                <td><?= e($p['nombre']) ?></td>
                <td><?= e($p['proveedor']) ?></td>
                <td><?= e($p['fecha_vencimiento']) ?></td>
                <td><?= number_format((float)$p['precio'], 2) ?></td>
                <td><?= e($p['stock']) ?></td>
                <td>
                    <a class="btn" href="form.php?id=<?= e($p['id']) ?>">Editar</a>
                    <form class="inline" method="post" action="eliminar.php"
                        onsubmit="return confirm('¿Eliminar este producto?');">
                        <input type="hidden" name="id" value="<?= e($p['id']) ?>">
                        <button class="rojo" type="submit">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>