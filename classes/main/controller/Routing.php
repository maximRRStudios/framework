<?php

namespace classes\main\controller;
use classes\main\core\Core;

/**
 * Роутинг
 */
class Routing
{
    const DEFAULT_METHOD = "init";

    protected $_method = "";

    public function __construct()
    {
        $core = Core::getInstance();
        $config = $core->config['routing'];
        $this->_method = $core->request->param($config['parameter'], self::DEFAULT_METHOD);
    }
}