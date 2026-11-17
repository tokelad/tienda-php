<?php
require_once __DIR__ . '/../vendor/autoload.php';

use config\Config;
use models\Producto;
use services\SessionService;
use services\ProductosService;
use services\CategoriasService;

$session = SessionService::getInstance();

if (!$session->isAdmin()) {
    header('Location: index.php');
    exit;
}

$db = Config::getInstance()->connect();
$productosService = new ProductosService($db);
$categoriasService = new CategoriasService($db);
$categorias = $categoriasService->findAll();

$errores = [];
$mensaje = "";

$marca = trim($_POST['marca'] ?? '');
$modelo = trim($_POST['modelo'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$precio = trim($_POST['precio'] ?? '');
$stock = trim($_POST['stock'] ?? '');
$categoriaId = $_POST['categoria'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validaciones
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
        $producto = new Producto(
            marca: $marca,
            modelo: $modelo,
            descripcion: $descripcion,
            precio: (float) $precio,
            stock: (int) $stock,
            categoriaId: $categoriaId
        );
        $productosService->save($producto);
        $mensaje = "Producto creado correctamente.";

        $marca = $modelo = $descripcion = $precio = $stock = $categoriaId = '';
    }
}

require_once 'header.php';
?>

<h1>Nuevo producto</h1>

<?php if ($mensaje != '') { ?>
    <div class="alert alert-success"><?php echo $mensaje; ?></div>
<?php } ?>

<form action="create.php" method="post" style="max-width: 600px;">
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

    <button type="submit" class="btn btn-success">Crear</button>
    <a href="index.php" class="btn btn-secondary">Volver</a>
</form>

<?php require_once 'footer.php'; ?>