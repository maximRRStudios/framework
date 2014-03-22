<?php
namespace classes\main\collection;

use IteratorAggregate;
use Traversable;
/**
 * Класс для работы с коллекциями объектов.
 */
abstract class Collection implements IteratorAggregate
{
    /**
     *  Массив с объектами.
     * @var array
     */
    protected $_members = array();

    /**
     * Флаг проверки, был ли выполнен обратный вызов.
     * @var boolean
     */
    private $_isLoaded = false;


    /**
     * (non-PHPdoc)
     * @see IteratorAggregate::getIterator()
     * @return CollectionIterator|Traversable
     */
    public function getIterator()
    {
        $this->_load();
        return new CollectionIterator($this);
    }


    /**
     * Метод для добавления нового экземпляра в коллекцию.
     * @param object $obj Объект.
     * @param string $key Индекс объекта.
     * @throws CollectionException
     */
    public function add($obj, $key = NULL)
    {
        $this->_load();
        $this->_add($obj, $key);
    }


    /**
     * Метод удаляет элемент из коллекции по его индексу.
     * @param string $key Индекс объекта.
     * @throws CollectionException
     */
    public function remove($key)
    {
        $this->_load();

        if (!isset($this->_members[$key])) {
            throw new CollectionException('Ключ "' . $key . '" не существует.');
        }
        unset($this->_members[$key]);
    }


    /**
     * Метод возвращает элемент коллекции по его индексу.
     * @param string $key Индекс объекта.
     * @return object
     * @throws CollectionException
     */
    public function get($key)
    {
        $this->_load();

        if (!isset($this->_members[$key])) {
            throw new CollectionException("Key '{$key}' does not exist.");
        }
        return $this->_members[$key];
    }

    /**
     * Метод возвращает всю коллекцию
     */
    public function getMembers()
    {
        $this->_load();

        return $this->_members;
    }

    /**
     * Метод возвращает количество элементов в коллекции.
     * @return integer
     */
    public function length()
    {
        $this->_load();

        return count($this->_members);
    }


    /**
     * Метод возвращает массив индексов из коллекции.
     * @return array
     */
    public function keys()
    {
        $this->_load();

        return array_keys($this->_members);
    }


    /**
     * Метод проверяет есть ли элемент по переданному индексу.
     * @param $key
     * @return bool
     */
    public function exists($key)
    {
        $this->_load();

        return isset($this->_members[$key]);
    }


    public function clear()
    {
        $this->_members = array();
    }

    public function reset()
    {
        $this->_isLoaded = false;
        $this->_members = array();
    }


    /**
     *  Метод для добавления нового экземпляра в коллекцию.
     * @param object $obj Объект.
     * @param string $key Индекс объекта.
     * @throws CollectionException
     */
    protected function _add($obj, $key = NULL)
    {
        if ($key) {
            if (isset($this->_members[$key])) {
                $name = get_class($this);
                throw new CollectionException(
                    "Ключ '{$key}' в коллекции '{$name}' уже занят."
                );
            } else {
                $this->_members[$key] = $obj;
            }
        } else {
            $this->_members[] = $obj;
        }
    }


    protected function _load()
    {
        if (!$this->_isLoaded) {
            $this->_isLoaded = true;
            $this->_onLoad();
        }
    }


    protected abstract function _onLoad();
}