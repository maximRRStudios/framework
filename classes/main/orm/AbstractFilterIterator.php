<?php
namespace classes\main\orm\properties;

use FilterIterator;
use Iterator;
abstract class AbstractFilterIterator extends FilterIterator
{
    protected $_params = array();

    public function __construct(Iterator $iterator, array $params)
    {
        parent::__construct($iterator);
        $this->_params = $params;
    }
}