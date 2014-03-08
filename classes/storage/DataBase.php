<?php
/**
 * Created by PhpStorm.
 * User: RiDeR
 * Date: 08.03.14
 * Time: 23:14
 */
class DataBase extends PDO
{

    public function __construct()
    {
        $app = Core::getInstance();
        $config = $app->config['db'];

        $option = array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '{$config['charset']}'",
            PDO::ATTR_STATEMENT_CLASS => array('DataBaseStatement'),
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 60,
        );

        $connectionString = "mysql:host={$config['host']};dbname={$config['dbname']}";
        parent::__construct($connectionString, $config['user'], $config['pass'], $option);
    }

    public function exec($statement)
    {
        $result = parent::exec($statement);
        return $result;
    }
}


class DataBaseStatement extends PDOStatement
{
    public function execute($input_parameters = null)
    {
        $return = parent::execute($input_parameters);

        return $return;
    }
}