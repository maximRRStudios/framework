<?php
namespace classes\main\collection;

use Iterator;
/**
 * Класс для получения списка элементов коллекции и их интеграционной  обработки.
 */
class CollectionIterator implements Iterator
{
    /**
     * Коллекция.
     * @var Collection
     */
    private $_collection;

    /**
     * Текущий индекс.
     * @var integer
     */
    private $_currIndex = 0;

    /**
     * Массив с ключами.
     * @var array
     */
    private $_keys;

    /**
     * Конструктор.
     * @param Collection $collection Коллекция.
     */
    public function __construct(Collection $collection)
    {
        $this->_collection = $collection;
        $this->_keys = $this->_collection->keys();
    }

    /**
     * (non-PHPdoc)
     * @see Iterator::rewind()
     */
    public function rewind()
    {
        $this->_currIndex = 0;
    }

    /**
     * (non-PHPdoc)
     * @see Iterator::key()
     */
    public function key()
    {
        return $this->_keys[$this->_currIndex];
    }

    /**
     * (non-PHPdoc)
     * @see Iterator::current()
     */
    public function current()
    {
        $key = $this->_keys[$this->_currIndex];
        return $this->_collection->get($key);
    }

    /**
     * (non-PHPdoc)
     * @see Iterator::next()
     */
    public function next()
    {
        $this->_currIndex++;
    }

    /**
     * (non-PHPdoc)
     * @see Iterator::valid()
     */
    public function valid()
    {
        return $this->_currIndex < $this->_collection->length();
    }
}