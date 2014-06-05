<?php
namespace classes\controllers;
/**
 * Контроллер ошибок
 */
class ErrorController
{
    public static function error404Action()
    {
        header("HTTP/1.0 404 Not Found");
    }
}