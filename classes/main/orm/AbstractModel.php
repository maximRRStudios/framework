<?php
namespace classes\main\orm;

use classes\main\orm\exceptions\FieldDoesNotExist;
use classes\main\orm\exceptions\ValidationError;
use classes\main\exceptions\NotImplementedError;
use Exception;
use DateTime;
use ArrayAccess;
/**
 * Модель
 *
 * @property int|string $id
 * @property AbstractModel $parent
 */
abstract class AbstractModel implements ArrayAccess
{
    const TYPE_INT = 'int';
    const TYPE_POSITIVE_INT = 'positive_int';
    const TYPE_BOOL = 'bool';
    const TYPE_STRING = 'string';
    const TYPE_DATETIME = 'datetime';
    const TYPE_DATE = 'date';
    const TYPE_FLOAT = 'float';

    const PROPERTY_TYPE = 'type';
    const PROPERTY_DB_NAME = 'db_name';
    const PROPERTY_METHOD = 'method';
    const PROPERTY_MIN_LENGTH = 'min_length';
    const PROPERTY_MAX_LENGTH = 'max_length';
    const PROPERTY_VALUE = 'value';

    /**
     * @var AbstractLibrary
     */
    public $library;

    /**
     * Свойства
     * @var array
     */
    protected $_properties = array();

    /**
     * Флаг модификации
     * @var bool
     */
    protected $_isModified = false;

    /**
     * Флаг нового
     * @var bool
     */
    protected $_isNew = true;

    /**
     * Локальный кеш
     * @var array
     */
    protected $_localCache = array();

    /**
     * Конструктор
     */
    public function __construct()
    {
        foreach ($this->_properties as $key => &$value) {
            if (!isset($value[self::PROPERTY_DB_NAME])) {
                $value[self::PROPERTY_DB_NAME] = $key;
            }
            if (!isset($value[self::PROPERTY_METHOD])) {
                $value[self::PROPERTY_METHOD] = false;
            }
            $value['value'] = null;
        }
    }

    /**
     * @param string $name
     * @return mixed
     * @throws FieldDoesNotExist
     * @throws ValidationError
     */
    public function __get($name)
    {
        if ($name == 'parent') {
            return $this->_getParent();
        }

        $class = get_class($this);

        if (!isset($this->_properties[$name])) {
            throw new FieldDoesNotExist("Field '{$name}' does not found in {$class}");
        }

        $field = $this->_properties[$name];

        if ($field['method']) {
            $method = '_get' . ucfirst($name);
            if (!method_exists($this, $method)) {
                throw new ValidationError("Field '{$name}' write-only in $class");
            }
            return $this->$method();
        } else {
            return $field['value'];
        }
    }

    /**
     * @param string $name
     * @param mixed $value
     * @throws FieldDoesNotExist
     * @throws ValidationError
     */
    public function __set($name, $value)
    {
        $class = get_class($this);

        if (!isset($this->_properties[$name])) {
            throw new FieldDoesNotExist("Field '{$name}' does not found in $class");
        }

        $field = &$this->_properties[$name];

        if (isset($field['method']) && $field['method']) {
            $method = '_set' . ucfirst($name);
            if (!method_exists($this, $method)) {
                throw new ValidationError("Field '{$name}' read-only in $class");
            }
            $this->$method($value);
        } else {
            $this->_validate($name, $field, $value);

            if ($field['value'] === $value) {
                return;
            }
            $field['value'] = $value;
        }

        if (!$this->_isNew) {
            $this->_isModified = true;
        }
    }

    public function __sleep()
    {
        return array('_properties');
    }

    public function __wakeup()
    {
        $this->_isNew = false;
        $this->_isModified = false;
    }

    public function offsetSet($offset, $value)
    {
        if (!is_null($offset)) {
            $this->$offset = $value;
        }
        throw new Exception;
    }

    public function offsetExists($offset)
    {
        return isset($this->_properties[$offset]);
    }

    public function offsetUnset($offset)
    {
        throw new Exception;
    }

    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    /**
     * Загрузка данных
     * @param array $properties Свойства
     */
    public function load(array $properties)
    {
        foreach ($this->_properties as $name => $param) {
            if (isset($param['method']) && $param['method']) {
                continue;
            }
            $value = $properties[$param[self::PROPERTY_DB_NAME]];
            $this->$name = $this->_normalizeByCode($this->_properties[$name], $value);
        }
        $this->_isModified = false;
        $this->_isNew = false;
    }

    /**
     * Устанавливает, что модель сохранена
     */
    public function setSaved()
    {
        if ($this->_isNew) {
            $this->library->update($this);
        }

        $this->_isNew = false;
        $this->_isModified = false;
    }

    /**
     * Новая ли модель
     * @return bool
     */
    public function isNew()
    {
        return $this->_isNew;
    }

    /**
     * Была ли модель изменена
     * @return bool
     */
    public function isModified()
    {
        return $this->_isModified;
    }

    /**
     * Удаление модели
     * @return bool
     */
    public function delete()
    {
        $this->library->delete($this);
    }

    public function asArrayBySql()
    {
        $result = array();
        foreach ($this->_properties as $property) {
            if ($property[self::PROPERTY_METHOD]) {
                continue;
            }
            $name = $property[self::PROPERTY_DB_NAME];
            $value = $property[self::PROPERTY_VALUE];
            if ($value === null) {
                $result[$name] = '';
            } elseif ($property[self::PROPERTY_TYPE] == self::TYPE_DATETIME) {
                /** @var $value DateTime */
                $result[$name] = $value->format('Y-m-d H:i:s');
            } elseif ($property[self::PROPERTY_TYPE] == self::TYPE_DATE) {
                /** @var $value DateTime */
                $result[$name] = $value->format('Y-m-d');
            } elseif ($property[self::PROPERTY_TYPE] == self::TYPE_BOOL) {
                $result[$name] = (int)$value;
            } else {
                $result[$name] = (string)$value;
            }
        }
        return $result;
    }

    /**
     * Валидация значений
     * @param string $name Имя
     * @param array $property Свойства
     * @param mixed $value Значение
     * @throws ValidationError
     */
    protected function _validate($name, array $property, $value)
    {
        $type = $property[self::PROPERTY_TYPE];
        if (in_array($type, array(self::TYPE_INT, self::TYPE_POSITIVE_INT)) && !is_int($value)) {
            throw new ValidationError("Field '{$name}' must be a int");
        }

        if ($type == self::TYPE_BOOL && !is_bool($value)) {
            throw new ValidationError("Field '{$name}' must be a bool");
        }

        if ($type == self::TYPE_STRING && !is_string($value)) {
            throw new ValidationError("Field '{$name}' must be a string");
        }

        if ($type == self::TYPE_POSITIVE_INT && $value < 0) {
            throw new ValidationError("Field '{$name}' must be a positive int");
        }

        if ($type == self::TYPE_FLOAT && !is_float($value)) {
            throw new ValidationError("Field '{$name}' must be a float");
        }

        if (isset($property[self::PROPERTY_MIN_LENGTH]) && mb_strlen($value) < $property[self::PROPERTY_MIN_LENGTH]) {
            throw new ValidationError("Field '{$name}' can not be shorter than {$property[self::PROPERTY_MIN_LENGTH]} characters in class " . get_class($this) . ".");
        }

        if (isset($property[self::PROPERTY_MAX_LENGTH]) && mb_strlen($value) > $property[self::PROPERTY_MAX_LENGTH]) {
            throw new ValidationError("Field '{$name}' can not be longer than {$property[self::PROPERTY_MAX_LENGTH]} characters in class " . get_class($this) . ".");
        }
    }

    protected function _normalizeByCode(array $property, $value)
    {
        switch ($property[self::PROPERTY_TYPE]) {
            case self::TYPE_INT:
            case self::TYPE_POSITIVE_INT:
                $value = (int)$value;
                break;
            case self::TYPE_STRING:
                $value = (string)$value;
                break;
            case self::TYPE_BOOL:
                $value = (bool)$value;
                break;
            case self::TYPE_DATETIME:
                if (!$value) {
                    $value = '2000-01-01 0:00:00';
                }
                $value = new DateTime($value);
                break;
            case self::TYPE_DATE:
                if (!$value) {
                    $value = '2000-01-01';
                }
                $value = new DateTime($value);
                break;
            case self::TYPE_FLOAT:
                $value = floatval($value);
                break;
            default:
                throw new NotImplementedError;
        }

        return $value;
    }

    /**
     * @throws NotImplementedError
     * @return AbstractModel
     */
    protected function _getParent()
    {
        throw new NotImplementedError;
    }
}