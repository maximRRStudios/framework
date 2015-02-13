<?php

namespace classes;
/**
 * Автозагрузчик по PSR-1
 */
class Autoload
{
    private $_directory;
    private $_prefix;
    private $_prefixLength;
    private $_vendersPath = array(
        'Smarty' => 'venders/smarty/Smarty.class.php',
        'Predis\\Autoloader' => 'venders/predis/Autoloader.php',
        'PHPMailer' => 'venders/phpmailer/class.phpmailer.php',
    );

    public function __construct($baseDirectory = __DIR__)
    {
        $this->_directory = $baseDirectory;
        $this->_prefix = __NAMESPACE__ . '\\';
        $this->_prefixLength = strlen($this->_prefix);
    }

    public static function register($prepend = false)
    {
        spl_autoload_register(array(new self, 'autoload'), true, $prepend);
        \Predis\Autoloader::register();
    }

    public function autoload($className)
    {
        if (0 === strpos($className, $this->_prefix)) {
            $parts = explode('\\', substr($className, $this->_prefixLength));
            $filepath = $this->_directory . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts) . '.php';
            if (is_file($filepath)) {
                require($filepath);
            }
        }
        if (isset($this->_vendersPath[$className])) {
            $filepath = $this->_directory . DIRECTORY_SEPARATOR . $this->_vendersPath[$className];
            
	       if (is_file($filepath)) {
                require($filepath);
            }
        }
    }
}