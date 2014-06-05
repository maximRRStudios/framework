<?php
namespace classes\controllers;
use classes\main\core\Core;

/**
 * Индекс
 */
class IndexController
{
    public static function indexAction()
    {
        $template = Core::getInstance()->template;
        $template->display("blocks/index/template.tpl");
    }
}