<?php

namespace classes\main\core;

use classes\main\storage\DataBase;
use classes\main\http\Request;
use classes\main\http\HttpClient;
use classes\main\controller\Routing;
use classes\main\template\Template;
use classes\main\storage\Apc;
use classes\main\storage\Memcached;
use classes\main\storage\Redis;
use classes\main\storage\File;
use classes\Autoload;
use DateTime;
use Exception;

require_once __DIR__ . "/../../Autoload.php";
/**
 * Class Core
 * @property DataBase $db класс БД
 * @property Request $request Запрос
 * @property HttpClient $httpClient Клиент http
 * @property Routing $route Роутинг
 * @property Template $template Смарти шаблоны
 * @property Apc $localCache Локальный кеш
 * @property Memcached $globalCache Глобальный кеш
 * @property Redis $redis Клиент redis
 * @property File $fileCache Файловый кеш
 */
class Core
{

    /**
     * Настройки
     * @var array
     */
    public $config;

    /**
     * текущее время
     * @var Datetime
     */
    public $datetime;

    /**
     * Версия
     * @var string
     */
    public $version;

    /**
     * Путь
     * @var string
     */
    public $basePath;

    /**
     * @var Register
     */
    private $_components;

    protected static $_instance;

    /**
     * Конструкто
     * @param $config
     */
    protected function __construct($config)
    {
        $this->_loadConfig($config);

        Autoload::register();
        $this->_components = new Register();
        $this->datetime = new DateTime();
    }


    /**
     * Загрузка конфига
     * @param $config
     */
    private function _loadConfig($config)
    {
        $this->version = $config['version'];
        $this->basePath = $config['basePath'];
        $this->config = $config['components'];
    }


    /**
     * Получение обьекта компанента
     * @param $name
     * @return mixed
     * @throws Exception
     */
    public function __get($name)
    {
        if ($this->_components->exist($name)) {
            return $this->_components->get($name);
        }

        $config = $this->config;
        if (isset($config['autoload'][$name])) {
            $className = $config['autoload'][$name];
            $component = new $className();
            $this->_components->add($name, $component);
            return $component;
        }

        throw new CoreException("Missing Component");
    }

    /**
     * Инициализация класса ядра
     * @param $config
     * @return Core
     * @throws Exception
     */
    public static function init($config)
    {
        if (self::$_instance) {
            throw new CoreException("Already initialized");
        }

        self::$_instance = new Core($config);
        return self::$_instance;
    }

    /**
     * Возвращает инициализированый обьект ядра
     * @return Core
     * @throws Exception
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            throw new CoreException("Not initialized");
        }
        return self::$_instance;
    }
}