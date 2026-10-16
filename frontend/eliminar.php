<?php
require_once __DIR__ . '/api.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_POST['id'];
[$codigo, $resp] = api_request('DELETE', "/productos/$id");

if ($codigo === 204) {
    header('Location: index.php?msg=' . urlencode('Producto eliminado correctamente'));
} else {
    header('Location: index.php?err=' . urlencode($resp['error'] ?? 'No se pudo eliminar el producto'));
}
exit;