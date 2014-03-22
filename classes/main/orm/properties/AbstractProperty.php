<?php
abstract class AbstractProperty
{
    protected $_dbName;

    abstract public function __construct($dbName, $defaultValue);
}