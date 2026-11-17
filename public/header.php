<?php
use services\SessionService;
$session = SessionService::getInstance();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Tienda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php">Mi Tienda</a>

        <div class="d-flex align-items-center">
            <a class="nav-link text-white me-3" href="index.php">Productos</a>

            <?php if ($session->isAdmin()) { ?>
                <a class="nav-link text-white me-3" href="create.php">Nuevo producto</a>
            <?php } ?>

            <span class="text-white me-3">
                Hola, <?php echo htmlspecialchars($session->getUsername()); ?>
            </span>

            <?php if ($session->isLoggedIn()) { ?>
                <a class="btn btn-outline-light btn-sm" href="logout.php">Logout</a>
            <?php } else { ?>
                <a class="btn btn-outline-light btn-sm" href="login.php">Login</a>
            <?php } ?>
        </div>
    </div>
</nav>

<main class="container">