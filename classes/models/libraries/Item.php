<?php
namespace classes\models\libraries;

use classes\main\orm\properties\AbstractModel;
/**
 * Шмот
 */
class Item extends AbstractModel
{
    protected $_properties = array(
        'id' => array(),
        'class' => array(),
        'type' => array(),
        'quality' => array(),
    );
}
