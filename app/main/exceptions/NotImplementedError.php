<?php
namespace App\Main\Exceptions;
/**
 * Ошибка генерится в случае, если вызвался код, требующий дополнительной
 * реализации
 */
class NotImplementedError extends AbstractCoreException
{
}