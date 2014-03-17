<?php
/**
 * точка входа для генерации
 */

require_once __DIR__ . "/../classes/main/Core.php";
$config = require_once __DIR__ . "/../config/base.php";

classes\main\Core::init($config);