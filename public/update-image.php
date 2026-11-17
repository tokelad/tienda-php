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

$productosService = new ProductosService(Config::getInstance()->connect());
$producto = $productosService->findById((int) $id);

if ($producto == null) {
    header('Location: index.php');
    exit;
}

require_once 'header.php';
?>

<h1>Cambiar imagen</h1>

<p><b>ID:</b> <?php echo $producto->id; ?></p>
<p><b>Marca:</b> <?php echo htmlspecialchars($producto->marca); ?></p>
<p><b>Modelo:</b> <?php echo htmlspecialchars($producto->modelo); ?></p>
<p><b>Imagen actual:</b></p>
<img src="<?php echo htmlspecialchars($producto->imagen); ?>" width="150" class="mb-3">

<form action="update_image_file.php" method="post" enctype="multipart/form-data" style="max-width: 400px;">
    <input type="hidden" name="id" value="<?php echo $producto->id; ?>">
    <div class="mb-3">
        <label for="imagen" class="form-label">Nueva imagen (JPG o PNG)</label>
        <input type="file" id="imagen" name="imagen" class="form-control" accept="image/jpeg, image/png" required>
    </div>
    <button type="submit" class="btn btn-primary">Actualizar imagen</button>
    <a href="index.php" class="btn btn-secondary">Volver</a>
</form>

<?php require_once 'footer.php'; ?>