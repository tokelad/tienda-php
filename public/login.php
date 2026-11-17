<?php
require_once __DIR__ . '/../vendor/autoload.php';

use config\Config;
use services\SessionService;
use services\UsersService;

$session = SessionService::getInstance();
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username == '' || $password == '') {
        $error = "Rellena el usuario y la contraseña.";
    } else {
        $usersService = new UsersService(Config::getInstance()->connect());
        try {
            $user = $usersService->authenticate($username, $password);
            $session->login($user);
            header('Location: index.php');
            exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

require_once 'header.php';
?>

<h1>Iniciar sesión</h1>

<?php if ($error != '') { ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php } ?>

<form action="login.php" method="post" style="max-width: 400px;">
    <div class="mb-3">
        <label for="username" class="form-label">Usuario</label>
        <input type="text" id="username" name="username" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" id="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Entrar</button>
</form>

<?php require_once 'footer.php'; ?>