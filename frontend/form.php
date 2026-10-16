<?php
require_once __DIR__ . '/api.php';

function e($valor): string
{
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$errores = [];
$producto = [
    'nombre' => '', 'proveedor' => '', 'fecha_vencimiento' => '',
    'precio' => '', 'stock' => '',
];

// Modo editar: cargar los datos actuales desde el backend
if ($id !== null) {
    [$codigo, $datos] = api_request('GET', "/productos/$id");
    if ($codigo !== 200) {
        header('Location: index.php?err=' . urlencode('Producto no encontrado'));
        exit;
    }
    $producto = $datos;
}

// Al enviar el formulario: POST (crear) o PUT (editar) hacia el backend
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $producto = [
        'nombre'            => trim($_POST['nombre'] ?? ''),
        'proveedor'         => trim($_POST['proveedor'] ?? ''),
        'fecha_vencimiento' => ($_POST['fecha_vencimiento'] ?? '') === '' ? null : $_POST['fecha_vencimiento'],
        'precio'            => ($_POST['precio'] ?? '') === '' ? null : (float)$_POST['precio'],
        'stock'             => ($_POST['stock'] ?? '') === '' ? null : (int)$_POST['stock'],
    ];

    if ($id === null) {
        [$codigo, $resp] = api_request('POST', '/productos', $producto);
    } else {
        [$codigo, $resp] = api_request('PUT', "/productos/$id", $producto);
    }

    if ($codigo === 200 || $codigo === 201) {
        $msg = $id === null ? 'Producto creado correctamente' : 'Producto actualizado correctamente';
        header('Location: index.php?msg=' . urlencode($msg));
        exit;
    }

    if ($codigo === 400 && isset($resp['violations'])) {
        foreach ($resp['violations'] as $v) {
            $errores[] = $v['message'];
        }
    } else {
        $errores[] = $resp['error'] ?? 'Ocurrió un error inesperado';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $id === null ? 'Nuevo producto' : 'Editar producto' ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1><?= $id === null ? 'Nuevo producto' : 'Editar producto #' . e($id) ?></h1>

    <?php foreach ($errores as $err): ?>
        <p class="error"><?= e($err) ?></p>
    <?php endforeach; ?>

    <form class="datos" method="post" action="">
        <label>Nombre
            <input type="text" name="nombre" maxlength="100" required value="<?= e($producto['nombre']) ?>">
        </label>
        <label>Proveedor
            <input type="text" name="proveedor" maxlength="100" required value="<?= e($producto['proveedor']) ?>">
        </label>
        <label>Fecha de vencimiento
            <input type="date" name="fecha_vencimiento" required value="<?= e($producto['fecha_vencimiento']) ?>">
        </label>
        <label>Precio
            <input type="number" name="precio" step="0.01" min="0" required value="<?= e($producto['precio']) ?>">
        </label>
        <label>Stock
            <input type="number" name="stock" step="1" min="0" required value="<?= e($producto['stock']) ?>">
        </label>
        <p>
            <button type="submit">Guardar</button>
            <a class="btn" href="index.php">Cancelar</a>
        </p>
    </form>
</body>
</html>