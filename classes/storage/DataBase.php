<?php

namespace classes\storage;

use classes\main\Core;
use PDO;
use PDOStatement;
/**
 * Класс работы с БД
 */
class DataBase extends PDO
{

    public function __construct()
    {
        $app = Core::getInstance();
        $config = $app->config['db'];

        $option = array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '{$config['charset']}'",
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 60,
        );

        $connectionString = "mysql:host={$config['host']};dbname={$config['dbname']}";
        parent::__construct($connectionString, $config['user'], $config['pass'], $option);
    }
}