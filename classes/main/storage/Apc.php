<?php
namespace classes\main\storage;

use classes\main\core\Core;
/**
 * Класс для работы с APC.
 * @method boolean add() add(string $key, mixed $value, int $ttl = 0) Добавляет новый ключ
 * @method boolean set() set(string $key, mixed $value, int $ttl = 0) Устанавливает значение ключа
 * @method mixed get() get(string $key) Возвращает значение ключа
 * @method bool delete() delete(string $key) Удаление ключа
 * @method bool clear() clear() Удаление всех ключей
 */
class Apc
{
    /**
     * Префикс ключа
     * @var string
     */
    protected $_prefix = '';

    /**
     * Конструктор
     */
    public function __construct()
    {
        $config = Core::getInstance()->config['cache_local'];
        $this->_prefix = (string)$config['prefix'];
    }

    /**
     * Метод добавляет новый ключ, если он не существует.
     * @param string $key Название ключа
     * @param mixed $value Значение
     * @param int $ttl Время жизни ключа
     * @return boolean
     */
    protected function _add($key, $value, $ttl = 0)
    {
        $key = $this->__getKey($key);

        return apc_add($key, $value, $ttl);
    }

    /**
     * Метод устанавливает новое значение ключа, если он существует.
     * @param string $key Название ключа
     * @param mixed $value Значение
     * @param int $ttl Время жизни ключа
     * @return boolean
     */
    protected function _set($key, $value, $ttl = 0)
    {
        $key = $this->__getKey($key);

        return apc_store($key, $value, $ttl);
    }

    /**
     * Метод возвращает значение ключа.
     * Если ключ не существует, то будет возвращено null.
     *
     * @param string $key Название ключа
     * @return mixed
     */
    protected function _get($key)
    {
        $key = $this->__getKey($key);

        return apc_fetch($key);
    }

    /**
     * Метод удаляет ключ.
     * @param string $key Название ключа
     * @return boolean
     */
    protected function _delete($key)
    {
        $key = $this->__getKey($key);

        return apc_delete($key);
    }

    /**
     * Метод удаляет все ключи.
     * @return boolean
     */
    protected function _clear()
    {
        return apc_clear_cache('user');
    }

    /**
     * Метод возвращяет ключ вместе с префиксом.
     * @param $key string Название ключа
     * @return string
     */
    private function __getKey($key)
    {
        return "{$this->_prefix}:{$key}";
    }
}