<?php
namespace classes\main\social;

use classes\main\exceptions\NotImplementedError;
/**
 * 'Менеджер социалок
 */
class SocialManager
{
    const VKONTAKTE = 1;
    const FACEBOOK = 2;
    const ODNOKLASSNIKI = 3;

    private $_classes = array(
        self::VKONTAKTE     => "classes\\main\\social\\Vk",
        self::ODNOKLASSNIKI => "classes\\main\\social\\Odnoklassniki",
        self::FACEBOOK      => "classes\\main\\social\\Facebook",
    );

    /**
     * класс API соц. сети
     * @var array
     */
    private $_social;


    public function getSocial($type)
    {
        if (!array_key_exists($type, $this->_classes))
            throw new NotImplementedError;

        $className = $this->_classes[$type];
        if (get_class($this->_social[$type]) == $className)
            return $this->_social[$type];

        $this->_social[$type] = new $className();
        return $this->_social[$type];
    }

}