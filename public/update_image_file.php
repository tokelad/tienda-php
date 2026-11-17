<?php
require_once __DIR__ . '/../vendor/autoload.php';

use config\Config;
use services\SessionService;
use services\ProductosService;

$session = SessionService::getInstance();

if (!$session->isAdmin() || $_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: index.php');
    exit;
}

$id = $_POST['id'] ?? '';
$archivo = $_FILES['imagen'] ?? null;

if (!is_numeric($id) || $archivo == null || $archivo['error'] != UPLOAD_ERR_OK) {
    header('Location: index.php');
    exit;
}

$config = Config::getInstance();
$productosService = new ProductosService($config->connect());
$producto = $productosService->findById((int) $id);

if ($producto == null) {
    header('Location: index.php');
    exit;
}

$tiposPermitidos = ['image/jpeg', 'image/png'];
$extensionesPermitidas = ['jpg', 'jpeg', 'png'];

$tipoMime = mime_content_type($archivo['tmp_name']);
$extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

if (!in_array($tipoMime, $tiposPermitidos) || !in_array($extension, $extensionesPermitidas)) {
    header('Location: index.php');
    exit;
}

$nombreArchivo = $producto->uuid . '.' . $extension;
$destino = $config->getUploadPath() . $nombreArchivo;

if (str_contains($producto->imagen, '/uploads/')) {
    $imagenAnterior = $config->getUploadPath() . basename($producto->imagen);
    if (file_exists($imagenAnterior) && basename($producto->imagen) != $nombreArchivo) {
        unlink($imagenAnterior);
    }
}

if (!move_uploaded_file($archivo['tmp_name'], $destino)) {
    header('Location: index.php');
    exit;
}

$producto->imagen = $config->getUploadUrl() . $nombreArchivo;
$productosService->update($producto);

header('Location: update-image.php?id=' . $producto->id);
exit;