<?php
namespace classes\main\mailer;

use classes\main\core\Core;
/**
 * Класс для работы с почтой
 * @property string $Subject The Subject of the message.
 * @property string $ErrorInfo Holds the most recent mailer error message.
 * @method boolean addAddress() addAddress(string $address, string $name = '') Add a "To" address.
 * @method boolean send() send() Create a message and send it.
 * @method boolean msgHTML() msgHTML(string $message, string $basedir = '', bool $advanced = false) Create a message from an HTML string.
 */
class Mailer
{
    /**
     * @var /PHPMailer
     */
    protected $_mc;

    /**
     * Конструктор
     */
    public function __construct()
    {
        $core = Core::getInstance();
        $config = $core->config['mailer'];

        $this->_mc = new \PHPMailer();
        if ($config['smtp']) {
            $this->_mc->isSMTP();
            $this->_mc->Host = $config['host'];
            $this->_mc->Port = $config['port'];
            $this->_mc->SMTPSecure = $config['encryption'];
            $this->_mc->SMTPAuth = $config['auth'];
            $this->_mc->Username = $config['login'];
            $this->_mc->Password = $config['pass'];
        }
        $this->_mc->setFrom('noreply@rrstudios.ru');
        $this->_mc->addReplyTo('noreply@rrstudios.ru');
    }

    public function __call($name, $args)
    {
        $result = call_user_func_array(array($this->_mc, $name), $args);
        return $result;
    }
}