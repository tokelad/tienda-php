<?php
require_once __DIR__ . '/../vendor/autoload.php';

use config\Config;
use services\SessionService;
use services\ProductosService;
use services\CategoriasService;

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

$db = Config::getInstance()->connect();
$productosService = new ProductosService($db);
$categoriasService = new CategoriasService($db);
$categorias = $categoriasService->findAll();

$producto = $productosService->findById((int) $id);
if ($producto == null) {
    header('Location: index.php');
    exit;
}

$errores = [];
$mensaje = "";

$marca = trim($_POST['marca'] ?? $producto->marca);
$modelo = trim($_POST['modelo'] ?? $producto->modelo);
$descripcion = trim($_POST['descripcion'] ?? ($producto->descripcion ?? ''));
$precio = trim($_POST['precio'] ?? $producto->precio);
$stock = trim($_POST['stock'] ?? $producto->stock);
$categoriaId = $_POST['categoria'] ?? $producto->categoriaId;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($marca == '') {
        $errores['marca'] = "La marca es obligatoria.";
    }
    if ($modelo == '') {
        $errores['modelo'] = "El modelo es obligatorio.";
    }
    if (!is_numeric($precio) || $precio < 0) {
        $errores['precio'] = "El precio debe ser un número mayor o igual que 0.";
    }
    if (!is_numeric($stock) || $stock < 0) {
        $errores['stock'] = "El stock debe ser un número mayor o igual que 0.";
    }
    if ($categoriaId == '') {
        $errores['categoria'] = "Elige una categoría.";
    }

    if (count($errores) == 0) {
        $producto->marca = $marca;
        $producto->modelo = $modelo;
        $producto->descripcion = $descripcion;
        $producto->precio = (float) $precio;
        $producto->stock = (int) $stock;
        $producto->categoriaId = $categoriaId;

        $productosService->update($producto);
        $mensaje = "Producto actualizado correctamente.";
    }
}

require_once 'header.php';
?>

<h1>Editar producto</h1>

<?php if ($mensaje != '') { ?>
    <div class="alert alert-success"><?php echo $mensaje; ?></div>
<?php } ?>

<form action="update.php?id=<?php echo $producto->id; ?>" method="post" style="max-width: 600px;">
    <div class="mb-3">
        <label for="marca" class="form-label">Marca</label>
        <input type="text" id="marca" name="marca" class="form-control"
               value="<?php echo htmlspecialchars($marca); ?>">
        <?php if (isset($errores['marca'])) { ?>
            <div class="text-danger"><?php echo $errores['marca']; ?></div>
        <?php } ?>
    </div>

    <div class="mb-3">
        <label for="modelo" class="form-label">Modelo</label>
        <input type="text" id="modelo" name="modelo" class="form-control"
               value="<?php echo htmlspecialchars($modelo); ?>">
        <?php if (isset($errores['modelo'])) { ?>
            <div class="text-danger"><?php echo $errores['modelo']; ?></div>
        <?php } ?>
    </div>

    <div class="mb-3">
        <label for="descripcion" class="form-label">Descripción</label>
        <textarea id="descripcion" name="descripcion" class="form-control"><?php echo htmlspecialchars($descripcion); ?></textarea>
    </div>

    <div class="mb-3">
        <label for="precio" class="form-label">Precio (€)</label>
        <input type="number" step="0.01" id="precio" name="precio" class="form-control"
               value="<?php echo htmlspecialchars($precio); ?>">
        <?php if (isset($errores['precio'])) { ?>
            <div class="text-danger"><?php echo $errores['precio']; ?></div>
        <?php } ?>
    </div>

    <div class="mb-3">
        <label for="stock" class="form-label">Stock</label>
        <input type="number" id="stock" name="stock" class="form-control"
               value="<?php echo htmlspecialchars($stock); ?>">
        <?php if (isset($errores['stock'])) { ?>
            <div class="text-danger"><?php echo $errores['stock']; ?></div>
        <?php } ?>
    </div>

    <div class="mb-3">
        <label class="form-label">Imagen (solo lectura)</label>
        <input type="text" class="form-control" value="<?php echo htmlspecialchars($producto->imagen); ?>" readonly>
    </div>

    <div class="mb-3">
        <label for="categoria" class="form-label">Categoría</label>
        <select id="categoria" name="categoria" class="form-select">
            <option value="">-- Elige una categoría --</option>
            <?php foreach ($categorias as $categoria) { ?>
                <option value="<?php echo $categoria->getId(); ?>"
                    <?php if ($categoria->getId() == $categoriaId) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($categoria->nombre); ?>
                </option>
            <?php } ?>
        </select>
        <?php if (isset($errores['categoria'])) { ?>
            <div class="text-danger"><?php echo $errores['categoria']; ?></div>
        <?php } ?>
    </div>

    <button type="submit" class="btn btn-warning">Guardar cambios</button>
    <a href="index.php" class="btn btn-secondary">Volver</a>
</form>

<?php require_once 'footer.php'; ?>