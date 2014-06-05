<?php
/**
 * точка входа
 * /index.php?route=action\controller
 */
use classes\main\core\Core;

require_once __DIR__ . "/../classes/main/core/Core.php";
$config = require_once __DIR__ . "/../config/base.php";

Core::init($config);
$route = Core::getInstance()->route;
$route->call();