<?php

namespace classes\models\user;

use classes\main\orm\properties\AbstractModel;

/**
 * Пользователь
 */
class User extends AbstractModel
{
    /**
     * Солдат
     */
    const SOLDIER = 1;

    /**
     * Снайпер
     */
    const SNIPER = 2;

    /**
     * Мнемоник
     */
    const MNEMONIC = 3;

    protected $_properties = array(
        'id' => array(
            self::PROPERTY_TYPE => self::TYPE_POSITIVE_INT,
            self::PROPERTY_DB_NAME => 'user_id',
        ),
        'level' => array(
            self::PROPERTY_TYPE => self::TYPE_POSITIVE_INT,
            self::PROPERTY_DB_NAME => 'level',
        ),
        'exp' => array(
            self::PROPERTY_TYPE => self::TYPE_POSITIVE_INT,
            self::PROPERTY_DB_NAME => 'exp',
        ),
        'health' => array(
            self::PROPERTY_TYPE => self::TYPE_POSITIVE_INT,
            self::PROPERTY_DB_NAME => 'health',
        ),
        'energy' => array(
            self::PROPERTY_TYPE => self::TYPE_POSITIVE_INT,
            self::PROPERTY_DB_NAME => 'energy',
        ),
        'class' => array(
            self::PROPERTY_TYPE => self::TYPE_POSITIVE_INT,
        ),
        'damage' => array(
            self::PROPERTY_METHOD => true,
        ),
    );


    /**
     * Урон персонажа
     */
    protected function _getDamage()
    {

    }
}