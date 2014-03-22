<?php
namespace classes\main\exceptions;

use Exception;
/**
 * Абстрактный класс исключений.
 * Все исключения в игре должны наследоваться от него.
 */
abstract class AbstractCoreException extends Exception
{
    public function __construct($message='', $code = 0)
    {
    }
}