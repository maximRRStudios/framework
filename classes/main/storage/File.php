<?php
namespace classes\main\storage;

use classes\main\core\Core;
/**
 * Класс для работы с файлом, как кешем.
 */
class File
{
    protected $_pathToFiles;

    /**
     * Конструктор
     */
    public function __construct()
    {
        $config = Core::getInstance()->config['cache_file'];
        $this->_pathToFiles = $config['path_to_files'];
    }

    /**
     * Возвращает содержимое файла
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        $path = "{$this->_pathToFiles}/{$key}.php";
        $result = require_once $path;

        return $result;
    }
}