<?php
require_once __DIR__ . '/../vendor/autoload.php';

use services\SessionService;

$session = SessionService::getInstance();
$session->logout();

header('Location: index.php');
exit;