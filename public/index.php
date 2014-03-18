<?php
/**
 * точка входа для генерации
 */

require_once __DIR__ . "/../classes/main/core/Core.php";
$config = require_once __DIR__ . "/../config/base.php";

\classes\main\core\Core::init($config);