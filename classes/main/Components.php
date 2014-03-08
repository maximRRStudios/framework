<?php
/**
 * Created by PhpStorm.
 * User: RiDeR
 * Date: 09.03.14
 * Time: 0:11
 */
class Components
{
    private $_items = array();

    public function add($name, $value, $rewrite = false)
    {
        if ($this->exist($name) && !$rewrite) {
            throw new ComponentsException("Can't add");
        }

        $this->_items[$name] = $value;
    }

    public function get($name)
    {
        if (!$this->exist($name)) {
            throw new ComponentsException("Not found");
        }

        return $this->_items[$name];
    }

    public function exist($name)
    {
        return array_key_exists($name, $this->_items);
    }

    public function delete($name)
    {
        if ($this->exist($name)) {
            unset($this->_items[$name]);
        }
    }
}