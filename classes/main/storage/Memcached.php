<?php
namespace classes\main\storage;

use classes\main\core\Core;
/**
 * Класс для работы с Memcached.
 *
 * @method boolean add() add(string $key, mixed $value, int $ttl = 0) Добавляет новый ключ
 * @method boolean set() set(string $key, mixed $value, int $ttl = 0) Устанавливает значение ключа
 * @method boolean setMulti() setMulti(array $items, int $ttl = 0) Устанавливает значения нескольким ключам
 * @method mixed get() get(string $key) Возвращает значение ключа
 * @method array getMulti() getMulti(array $keys) Возвращает значения нескольких ключей
 * @method int increment() increment(string $key, int $offset = 1) Увеличивает значение ключа и возвращает предыдущее значение
 * @method bool delete() delete(string $key) Удаление ключа
 * @method bool deleteMulti() deleteMulti(array $keys, int $time = 0) Удаляет несколько элементов
 */
class Memcached
{
    /**
     * @var \Memcached
     */
    protected $_mc;

    /**
     * Конструктор
     */
    public function __construct()
    {
        $app = Core::getInstance();
        $config = $app->config['cache_global'];

        $this->_mc = new \Memcached();
        $this->_mc->addServers($config['servers']);
        $this->_mc->setOption(\Memcached::OPT_COMPRESSION, (bool)$config['compression']);
        $this->_mc->setOption(\Memcached::OPT_PREFIX_KEY, (string)$config['prefix']);
    }

    /**
     * Метод удаляет все ключи.
     * @return boolean
     */
    public function clear()
    {
        return $this->_mc->flush();
    }
}