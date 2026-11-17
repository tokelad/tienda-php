<?php
require_once __DIR__ . '/../vendor/autoload.php';

use config\Config;
use services\SessionService;
use services\ProductosService;

$session = SessionService::getInstance();

if (!$session->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? '';
if (!is_numeric($id)) {
    header('Location: index.php');
    exit;
}

$productosService = new ProductosService(Config::getInstance()->connect());
$producto = $productosService->findById((int) $id);

if ($producto == null) {
    header('Location: index.php');
    exit;
}

require_once 'header.php';
?>

<h1>Detalles del producto</h1>

<div class="row">
    <div class="col-md-4">
        <img src="<?php echo htmlspecialchars($producto->imagen); ?>" class="img-fluid" alt="Imagen del producto">
    </div>
    <div class="col-md-8">
        <dl class="row">
            <dt class="col-sm-3">ID</dt>
            <dd class="col-sm-9"><?php echo $producto->id; ?></dd>

            <dt class="col-sm-3">Marca</dt>
            <dd class="col-sm-9"><?php echo htmlspecialchars($producto->marca); ?></dd>

            <dt class="col-sm-3">Modelo</dt>
            <dd class="col-sm-9"><?php echo htmlspecialchars($producto->modelo); ?></dd>

            <dt class="col-sm-3">Descripción</dt>
            <dd class="col-sm-9"><?php echo htmlspecialchars($producto->descripcion ?? ''); ?></dd>

            <dt class="col-sm-3">Precio</dt>
            <dd class="col-sm-9"><?php echo $producto->precio; ?> €</dd>

            <dt class="col-sm-3">Stock</dt>
            <dd class="col-sm-9"><?php echo $producto->stock; ?></dd>

            <dt class="col-sm-3">Categoría</dt>
            <dd class="col-sm-9"><?php echo htmlspecialchars($producto->categoriaNombre); ?></dd>
        </dl>
    </div>
</div>

<a href="index.php" class="btn btn-secondary">Volver</a>

<?php require_once 'footer.php'; ?>