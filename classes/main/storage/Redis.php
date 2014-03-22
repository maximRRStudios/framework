<?php
namespace classes\main\storage;

use classes\main\core\Core;
/**
 * Класс для работы с редисом
 * @method bool set($key, $value)
 *
 * @method bool sadd($key, $member)
 * @method array smembers($key)
 *
 * @method int lpush($key, $value)
 * @method int rpush($key, $value)
 * @method mixed lpop($key)
 * @method mixed rpop($key)
 * @method int llen($key)
 *
 * @method null flushAll()
 * @method null flushDb()
 */
class Redis extends \Predis\Client
{
    public function __construct()
    {
        $app = Core::getInstance();
        $config = $app->config['redis'];

        parent::__construct(
            $config['host'], array('prefix' => $config['prefix'], 'database' => $config['db'])
        );
    }
}