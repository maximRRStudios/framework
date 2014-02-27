<?php
/**
 * Created by RiDeR
 * Date: 25.02.14
 * Time: 6:53
 * Класс ядра
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

    protected static $_instance;

    /**
     * Конструкто
     * @param $config
     */
    protected function __construct($config)
    {
        $this->_loadConfig($config);

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