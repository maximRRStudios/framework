<?php

namespace classes\main;
/**
 * Компоненты
 */
class Components
{

    /**
     * Массив компонентов
     * @var array
     */
    private $_items = array();

    /**
     * Добавляет компонент в массив
     * @param string $name имя компонента
     * @param mixed $value Обьект
     * @param bool $rewrite перезапись или нет
     * @throws ComponentsException
     */
    public function add($name, $value, $rewrite = false)
    {
        if ($this->exist($name) && !$rewrite) {
            throw new ComponentsException("Can't add");
        }

        $this->_items[$name] = $value;
    }

    /**
     * Получает компонент
     * @param string $name имя компонента
     * @return mixed компоненты
     * @throws ComponentsException
     */
    public function get($name)
    {
        if (!$this->exist($name)) {
            throw new ComponentsException("Not found");
        }

        return $this->_items[$name];
    }

    /**
     * Проверяет существует ли обьект компонента
     * @param string $name имя компонента
     * @return bool
     */
    public function exist($name)
    {
        return array_key_exists($name, $this->_items);
    }

    /**
     * Удаляет обьект
     * @param string $name имя компонента
     */
    public function delete($name)
    {
        if ($this->exist($name)) {
            unset($this->_items[$name]);
        }
    }
}