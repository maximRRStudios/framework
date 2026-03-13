<?php

namespace App\Main\Core;

use App\Main\Core\Register;
use App\Main\Core\Exceptions\CoreException;
use App\Main\Storage\DataBase;
use App\Main\Http\Request;
use App\Main\Http\HttpClient;
use App\Main\Controller\Routing;
use App\Main\Template\Template;
use App\Main\Storage\Apc;
use App\Main\Storage\Memcached;
use App\Main\Storage\File;
use App\Main\Social\SocialManager;
use DateTime;
use Exception;

require_once __DIR__ . "/../../vendor/autoload.php";
/**
 * Class Core
 * @property DataBase $db класс БД
 * @property Request $request Запрос
 * @property HttpClient $httpClient Клиент http
 * @property Routing $route Роутинг
 * @property Template $template Смарти шаблоны
 * @property Apc $localCache Локальный кеш
 * @property Memcached $globalCache Глобальный кеш
 * @property File $fileCache Файловый кеш
 * @property SocialManager $social Социалки
 */
class Core
{
    public readonly string $version;
    public readonly string $basePath;
    public readonly array $config;

    private Register $container;
    public DateTime $datetime;

    private static ?self $_instance = null;

    private function __construct(array $config)
    {
        $this->_loadConfig($config);
        $this->container = new Register();
        $this->datetime = new DateTime();
    }

    private function _loadConfig(array $config): void
    {
        $this->version = $config['version'];
        $this->basePath = $config['basePath'];
        $this->config = $config['components'] ?? [];
    }

    /**
     * @throws NotFoundException
     * @throws ContainerException
     */
    public function __get(string $name): object
    {
        if ($this->container->has($name)) {
            $result = $this->container->get($name);
            return $result;
        }

        $autoload = $this->config['autoload'][$name] ?? null;
        if (!$autoload) {
            throw new CoreException("Component '$name' not defined");
        }

        if (!class_exists($autoload)) {
            throw new CoreException("Class '$autoload' not found");
        }

        $this->container->set($name, fn() => new $autoload($this));

        $result = $this->container->get($name);

        return $result;
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