<?php
/**
 * Created by RiDeR.
 * Date: 25.02.14
 * Time: 6:58
 * точка входа для генерации
 */

require_once __DIR__ . "/../classes/storage/DataBase.php";
require_once __DIR__ . "/../classes/main/Core.php";
require_once __DIR__ . "/../classes/main/Components.php";
require_once __DIR__ . "/../classes/main/ComponentsException.php";
$config = require_once __DIR__ . "/../config/base.php";

Core::init($config);
