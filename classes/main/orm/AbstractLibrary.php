<?php
namespace classes\main\orm\properties;

use classes\main\collection\Collection;
use classes\main\core\Core;
use classes\main\exceptions\NotInCacheException;
use classes\main\orm\exceptions\ValidationError;
use classes\main\storage\DataBase;
use classes\main\storage\Apc;
use classes\main\storage\Memcached;
use PDO;
/**
 * Библиотека
 */
abstract class AbstractLibrary extends Collection
{
    /**
     * Кешировать в глобальном кеше
     */
    const CACHE_GLOBAL = 'globalCache';

    /**
     * Кешировать в локальном кеше
     */
    const CACHE_LOCAL = 'localCache';

    /**
     * Кешировать в файловом кеше
     */
    const CACHE_FILE = 'fileCache';

    /**
     * Кеширование не использовать
     */
    const CACHE_NONE = null;

    /**
     * Линк БД
     * @var DataBase
     */
    protected $_db;

    /**
     * Название таблицы БД
     * @var string
     */
    protected $_dbTable;

    /**
     * Название идентификатора в БД
     * @var string
     */
    protected $_dbIdName = 'id';

    /**
     * Кеш
     * @var Apc|Memcached|null
     */
    protected $_cache;

    /**
     * Тип кеша
     * @var string
     */
    protected $_cacheType = self::CACHE_GLOBAL;

    /**
     * Версия кеша
     * @var int
     */
    protected $_cacheVersion = 1;

    /**
     * Ключ кеша
     * @var string
     */
    protected $_cacheKey;

    /**
     * Время жизни ключа
     * @var int
     */
    protected $_cacheTtl = 3600;

    /**
     * Имя класса модели
     * @var string
     */
    protected $_modelName;

    /**
     * Библиотека только для чтения
     * @var string
     */
    protected $_readOnly = false;

    /**
     * Библиотека была загружена из кеша
     * @var bool
     */
    protected $_isLoadFromCache = false;

    protected $_modelToRemove = array();

    protected $_sqlSelect = 'SELECT * FROM `#table#`';

    protected $_sqlInsert = 'INSERT INTO `#table#` SET #set#';

    protected $_sqlUpdate = 'UPDATE `#table#` SET #set# WHERE `id` = :id';

    protected $_sqlDelete = 'DELETE FROM `#table#` WHERE `id` = :id';

    /**
     * Конструктор
     */
    public function __construct()
    {
        $app = Core::getInstance();
        $this->_db = $app->db;

        if ($this->_cacheType) {
            $cacheType = $this->_cacheType;
            $this->_cache = $app->$cacheType;
        }
        $this->_cacheKey = get_class($this);

        $this->_modelName = str_replace('Library', '', get_class($this));
    }

    public function __destruct()
    {
        $this->save();
    }

    public function __get($name)
    {
        $method = '_get' . ucfirst($name);
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        throw new ValidationError("Field '{$name}' does not found");
    }

    public function __call($name, $args) {
        $method = substr($name, strlen($name) - 6);
        if ($method != 'Filter') {
            throw new ValidationError("Method '{$name}' does not found");
        }

        $class = get_class($this);
        $class = $class . ucfirst($name);

        if (!class_exists($class)) {
            throw new ValidationError("Method '{$name}' does not found");
        }

        return new $class($this->getIterator(), $args);
    }

    /**
     * Добавляет новую модель в библиотеку.
     * @param AbstractModel $model Модель
     */
    public function add(AbstractModel $model)
    {
        parent::add($model, $model->id);
        $model->library = $this;
    }

    public function delete(AbstractModel $model)
    {
        $this->remove($model->id);
        $this->_modelToRemove[$model->id] = $model;
    }

    /**
     * Обновляет модель в библиотеке.
     * @param AbstractModel $model Модель
     */
    public function update(AbstractModel $model) {
        /** @var $item AbstractModel */
        foreach ($this as $key => $item) {
            if ($item->id != $model->id) {
                continue;
            }

            if ($key == $model->id) {
                break;
            }

            $this->remove($key);
            $this->add($model);
        }
    }

    /**
     * Сохранение данных
     */
    public function save()
    {
        if (!$this->_readOnly) {
            $this->_saveInStorage();
            $this->_saveInCache();

            /** @var $item AbstractModel */
            foreach ($this as $item) {
                $item->setSaved();
            }
        }
    }

    public function clearCache()
    {
        $key = $this->_getKey();
        $this->_cache->delete($key);
    }

    /**
     * Загрузка данных
     */
    protected function _onLoad()
    {
        try {
            $this->_onLoadFromCache();
            $this->_isLoadFromCache = true;
        } catch (NotInCacheException $e) {
            $this->_onLoadFromStorage();

            // Сохраняем данные в кеше
            if ($this->_readOnly) {
                $this->_saveInCache();
            }
        }
    }

    /**
     * Загрузка данных из БД
     */
    protected function _onLoadFromStorage($sql = null, array $params = array())
    {
        if (!$sql) {
            $sql = str_replace('#table#', $this->_dbTable, $this->_sqlSelect);
        }

        // Получаем данные
        if (count($params)) {
            $dbResult = $this->_db->prepare($sql);
            $dbResult->execute($params);
        } else {
            $dbResult = $this->_db->query($sql);
        }
        $arResult = $dbResult->fetchAll(PDO::FETCH_ASSOC);

        // Заполняем коллекцию
        foreach ($arResult as $item) {
            /** @var $model AbstractModel */
            $model = new $this->_modelName();
            $model->load($item);
            $model->library = $this;
            $this->_add($model, $model->id);
        }
    }

    /**
     * Загрузка данных из кеша
     * @throws NotInCacheException
     */
    protected function _onLoadFromCache()
    {
        if (!$this->_cache) {
            throw new NotInCacheException;
        }

        $key = $this->_getKey();
        $result = $this->_cache->get($key);
        if ($result === false) {
            throw new NotInCacheException;
        }

        if ($this->_cacheType == self::CACHE_FILE) {
            foreach ($result as $item) {
                /** @var $model AbstractModel */
                $model = new $this->_modelName();
                $model->load($item);
                $this->_add($model, $model->id);
            }
        } else {
            $this->_members = $result;
        }
    }

    /**
     * Сохранение данных в кеш
     */
    protected function _saveInCache()
    {
        if (in_array($this->_cache, array(self::CACHE_NONE, self::CACHE_FILE))) {
            return;
        }

        /** @var $item AbstractModel */
        $needSave = false;
        foreach ($this as $item) {
            if ($item->isModified() || $item->isNew()) {
                $needSave = true;
                break;
            }
        }

        if ($this->_isLoadFromCache && !$needSave) {
            return;
        }

        $items = $this->_members;
        $key = $this->_getKey();
        $this->_cache->set($key, $items, $this->_cacheTtl);
    }

    /**
     * Сохранение данных в БД
     */
    protected function _saveInStorage()
    {
        $this->_insertInStorage();
        $this->_updateInStorage();
        $this->_deleteInStorage();
    }

    protected function _insertInStorage()
    {
        /** @var $item AbstractModel */
        foreach ($this as $item) {
            if (!$item->isNew()) {
                continue;
            }

            list($values, $set) = $this->_getSqlParams($item);

            $sql = str_replace(
                array('#table#', '#set#'),
                array($this->_dbTable, $set),
                $this->_sqlInsert
            );

            $query = $this->_db->prepare($sql);
            $query->execute($values);

            try {
                $item->id = (int)$this->_db->lastInsertId();
            } catch (ValidationError $error) {
            }

            $this->_insertInStorageExtra($item);
        }
    }

    protected function _updateInStorage()
    {
        /** @var $item AbstractModel */
        foreach ($this as $item) {
            if (!$item->isModified()) {
                continue;
            }

            list($values, $set) = $this->_getSqlParams($item);

            $sql = str_replace(
                array('#table#', '#set#'),
                array($this->_dbTable, $set),
                $this->_sqlUpdate
            );

            $query = $this->_db->prepare($sql);
            $query->execute($values);

            $this->_updateInStorageExtra($item);
        }
    }

    protected function _deleteInStorage()
    {
        /** @var $item AbstractModel */
        foreach ($this->_modelToRemove as $item) {
            $sql = str_replace('#table#', $this->_dbTable, $this->_sqlDelete);

            $values = $this->_getSqlParams($item);
            $values = $values[0];

            $arValues = array();
            foreach ($values as $key => $value) {
                if (strpos($sql, $key) !== false) {
                    $arValues[$key] = $value;
                }
            }

            $query = $this->_db->prepare($sql);
            $query->execute($arValues);

            $this->_deleteInStorageExtra($item);
        }

        $this->_modelToRemove = array();
    }

    protected function _insertInStorageExtra(AbstractModel $model)
    {
    }

    protected function _updateInStorageExtra(AbstractModel $model)
    {
    }

    protected function _deleteInStorageExtra(AbstractModel $model)
    {
    }

    protected function _getSqlParams(AbstractModel $item)
    {
        $values = array();
        $set = array();
        foreach ($item->asArrayBySql() as $name => $value) {
            if ($value === null) {
                $value = 'null';
            }

            $set[] = "`{$name}` = :{$name}";
            $values[":{$name}"] = $value;
        }
        $set = implode(', ', $set);

        return array($values, $set);
    }

    /**
     * Получение ключа
     * @return string
     */
    protected function _getKey()
    {
        return "{$this->_cacheKey}:{$this->_cacheVersion}";
    }
}