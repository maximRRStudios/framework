<?php
namespace App\Controllers;
use App\Main\Core\Core;

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