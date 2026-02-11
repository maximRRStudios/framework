<?php
/**
 * точка входа
 * /index.php?route=action\controller
 */
use App\Main\Core\Core;

require_once __DIR__ . "/../app/main/core/Core.php";
$config = require_once __DIR__ . "/../config/base.php";

$app = Core::init($config);
$route = $app->route;
$route->call();
