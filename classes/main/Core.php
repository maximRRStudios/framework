<?php
/**
 * Created by RiDeR
 * Date: 25.02.14
 * Time: 6:53
 * Класс ядра
 */

/**
 * Class Core
 * @property DataBase $db класс БД
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
     * @var Components
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

        $this->_components = new Components();
        $this->datetime = new DateTime();
    }


    /**
     * Загрузка конфига
     * @param $config
     */
    private function _loadConfig($config)
    {
        $this->config = $config['components'];
    }


    /**
     * Получение обьекта компанента
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if ($this->_components->exist($name)) {
            $this->_components->get($name);
        }

        $config = $this->config;
        if (isset($config['autoload'][$name])) {
            $className = $config['autoload'][$name];
            $component = new $className();
            $this->_components->add($name, $component);
            return $component;
        }
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