<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once 'header.php';

use config\Config;
use services\ProductosService;

$productosService = new ProductosService(Config::getInstance()->connect());

$busqueda = trim($_GET['busqueda'] ?? '');
$productos = $productosService->findAllWithCategoryName($busqueda);
?>

<h1>Bienvenido, <?php echo htmlspecialchars($session->getUsername()); ?></h1>

<form action="index.php" method="get" class="d-flex mb-3">
    <input type="text" name="busqueda" class="form-control me-2"
           placeholder="Buscar por marca o modelo"
           value="<?php echo htmlspecialchars($busqueda); ?>">
    <button type="submit" class="btn btn-primary">Buscar</button>
</form>

<?php if ($session->isAdmin()) { ?>
    <a href="create.php" class="btn btn-success mb-3">Nuevo producto</a>
<?php } ?>

<table class="table table-striped">
    <tr>
        <th>ID</th>
        <th>Imagen</th>
        <th>Marca</th>
        <th>Modelo</th>
        <th>Categoría</th>
        <th>Precio</th>
        <th>Stock</th>
        <th>Acciones</th>
    </tr>

    <?php foreach ($productos as $producto) { ?>
        <tr>
            <td><?php echo $producto->id; ?></td>
            <td><img src="<?php echo htmlspecialchars($producto->imagen); ?>" width="60"></td>
            <td><?php echo htmlspecialchars($producto->marca); ?></td>
            <td><?php echo htmlspecialchars($producto->modelo); ?></td>
            <td><?php echo htmlspecialchars($producto->categoriaNombre); ?></td>
            <td><?php echo $producto->precio; ?> €</td>
            <td><?php echo $producto->stock; ?></td>
            <td>
                <?php if ($session->isLoggedIn()) { ?>
                    <a href="details.php?id=<?php echo $producto->id; ?>" class="btn btn-info btn-sm">Detalles</a>
                <?php } ?>

                <?php if ($session->isAdmin()) { ?>
                    <a href="update.php?id=<?php echo $producto->id; ?>" class="btn btn-warning btn-sm">Editar</a>
                    <a href="update-image.php?id=<?php echo $producto->id; ?>" class="btn btn-secondary btn-sm">Imagen</a>
                    <a href="delete.php?id=<?php echo $producto->id; ?>" class="btn btn-danger btn-sm"
                       onclick="return confirm('¿Seguro que quieres eliminar este producto?');">Eliminar</a>
                <?php } ?>
            </td>
        </tr>
    <?php } ?>
</table>

<?php if (count($productos) == 0) { ?>
    <p>No se han encontrado productos.</p>
<?php } ?>

<?php if ($session->isLoggedIn()) { ?>
    <p>Número de visitas: <?php echo $session->getVisitas(); ?></p>
    <p>Último login: <?php echo $session->getLastLogin(); ?></p>
<?php } ?>

<?php require_once 'footer.php'; ?>