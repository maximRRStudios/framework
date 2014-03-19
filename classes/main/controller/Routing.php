<?php

namespace classes\main\controller;

use classes\main\core\Core;
/**
 * Роутинг
 */
class Routing
{
    const DEFAULT_METHOD = "indexAction\\index";
    const DEFAULT_CONTROLLER = "IndexController";
    const DEFAULT_ACTION = "indexAction";
    const ERROR_CONTROLLER = "ErrorController";
    const ERROR_404_ACTION = "error404Action";
    const CONTROLLERS_PATH = "classes\\controllers\\";

    protected $_method = "";
    protected $_action = "";
    protected $_controller = "";

    public function __construct()
    {
        $core = Core::getInstance();
        $config = $core->config['routing'];
        $this->_method = $core->request->param($config['parameter'], self::DEFAULT_METHOD);

        $controller = $this->_getController();
        $this->_action = $controller[0];
        $this->_controller = $controller[1];
    }

    public function call()
    {
        try {
            $controller = $this->_controller;
            $action = $this->_action;
            $controller::$action();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    protected function _getController()
    {
        $core = Core::getInstance();
        $config = $core->config['routing'];
        $method = $core->request->param($config['parameter'], '');

        if (!$method) {
            return array(self::DEFAULT_ACTION, self::CONTROLLERS_PATH . self::DEFAULT_CONTROLLER);
        }

        $tmp = explode('\\', $method);
        $controller =  self::CONTROLLERS_PATH . ucfirst($tmp[1]) . "Controller";
        $action = strtolower($tmp[0]) . "Action";

        if (!class_exists($controller)
            || !method_exists($controller, $action)) {
            return array(self::ERROR_404_ACTION, self::CONTROLLERS_PATH . self::ERROR_CONTROLLER);
        }

        return array($action, $controller);
    }
}