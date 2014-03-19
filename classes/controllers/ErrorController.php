<?php
namespace classes\controllers;
/**
 * Контроллер ошибок
 */
class ErrorController
{
    public static function error404Action()
    {
        echo "404";//throw new \Exception();
    }
}