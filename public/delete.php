<?php
require_once __DIR__ . '/../vendor/autoload.php';

use config\Config;
use services\SessionService;
use services\ProductosService;

$session = SessionService::getInstance();

if (!$session->isAdmin()) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'] ?? '';
if (!is_numeric($id)) {
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

if (str_contains($producto->imagen, '/uploads/')) {
    $rutaImagen = $config->getUploadPath() . basename($producto->imagen);
    if (file_exists($rutaImagen)) {
        unlink($rutaImagen);
    }
}

$productosService->deleteById($producto->id);

header('Refresh: 2; url=index.php');
require_once 'header.php';
?>

<div class="alert alert-success">
    Producto "<?php echo htmlspecialchars($producto->marca . ' ' . $producto->modelo); ?>" eliminado correctamente.
    Volviendo al listado...
</div>

<?php require_once 'footer.php'; ?>