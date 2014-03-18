<?php

namespace classes\main\http;

use classes\main\exceptions\DoesNotFound;
/**
 * Класс для работы с http запросом
 */
class Request
{
    /**
     * Client document date.
     * @var integer
     */
    protected $_HTTPDate;

    /**
     * Client document ETag.
     * @var string
     */
    protected $_HTTPETag;

    /**
     * If agent accepts GZip compressed data.
     * @var bool
     */
    protected $_acceptGZip;

    /**
     * Agent IP.
     * @var float
     */
    protected $_ip;

    /**
     * Date of latest request on client side.
     * @return integer
     */
    public function modifiedSince()
    {
        if (isset($this->_HTTPDate)) {
            return $this->_HTTPDate;
        }

        if (!isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            return ($this->_HTTPDate = 0);
        }

        $a = explode(' ', $_SERVER['HTTP_IF_MODIFIED_SINCE']);
        $b = explode(':', $a[4]);

        $months = array(
            'Jan' => 1,
            'Feb' => 2,
            'Mar' => 3,
            'Apr' => 4,
            'May' => 5,
            'Jun' => 6,
            'Jul' => 7,
            'Aug' => 8,
            'Sep' => 9,
            'Oct' => 10,
            'Nov' => 11,
            'Dec' => 12
        );

        return ($this->_HTTPDate = gmmktime(
            $b[0],
            $b[1],
            $b[2],
            $months[$a[2]],
            $a[1],
            $a[3]
        ));
    }

    /**
     * ETag of latest request on client side.
     * @return string
     */
    public function ETag()
    {
        if (isset($this->_HTTPETag)) {
            return $this->_HTTPETag;
        }

        $this->_HTTPETag = !isset($_SERVER['HTTP_IF_NONE_MATCH']);
        if ($this->_HTTPETag) {
            return '';
        } else {
            return $_SERVER['HTTP_IF_NONE_MATCH'];
        }
    }

    /**
     * Check if agent accepts GZipped data.
     * @return bool
     */
    public function acceptGZip()
    {
        if (isset($this->_acceptGZip)) {
            return $this->_acceptGZip;
        }

        return ($this->_acceptGZip = isset($_SERVER['HTTP_ACCEPT_ENCODING'])
            && false !== strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'));
    }

    /**
     * Get connection protocol.
     * @return string
     */
    public function protocol()
    {
        return (string)$_SERVER['SERVER_PROTOCOL'];
    }

    /**
     * Check if connected via proxy.
     * @return bool
     */
    public function viaProxy()
    {
        if (!isset($_SERVER['HTTP_VIA'])) {
            return false;
        } else {
            return (bool)$_SERVER['HTTP_VIA'];
        }
    }

    /**
     * Get request URL.
     * @return string
     */
    public function url()
    {
        return $this->host() . $_SERVER['REQUEST_URI'];
    }

    /**
     * Get host HTTP parameter.
     * @return string
     */
    public function host()
    {
        return $_SERVER['HTTP_HOST'];
    }

    /**
     * Get request URI.
     * @return string
     */

    public function uri()
    {
        $url = parse_url($_SERVER['REQUEST_URI']);
        return $url['path'];
    }

    /**
     * Get user-agent HTTP parameter.
     * @return string
     */
    public function ua()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    /**
     * Get IP.
     * @return mixed
     */
    public function ip()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Get referer HTTP parameter.
     * @return string
     */
    public function referer()
    {
        return !isset($_SERVER['HTTP_REFERER']) ? '' : $_SERVER['HTTP_REFERER'];
    }

    /**
     * Return REQUEST parameter or default value.
     * @param string $name REQUEST parameter name.
     * @param mixed $defVal Default value.
     * @return mixed
     * @throws DoesNotFound
     */
    public function param($name, $defVal = null)
    {
        if (isset($_REQUEST[$name])) {
            return $_REQUEST[$name];
        }

        if (!is_null($defVal)) {
            return $defVal;
        }

        throw new DoesNotFound("REQUEST parameter '$name' not found.");
    }

    /**
     * Return GET parameter or default value.
     * @param string $name GET parameter name.
     * @param mixed $defVal Default value.
     * @return mixed
     * @throws DoesNotFound
     */
    public function get($name, $defVal = null)
    {
        if (isset($_GET[$name])) {
            return $_GET[$name];
        }

        if (!is_null($defVal)) {
            return $defVal;
        }

        throw new DoesNotFound("GET parameter '{$name}' not found.");
    }

    /**
     * Return POST parameter or default value.
     * @param string $name POST parameter name.
     * @param mixed $defVal Default value.
     * @return mixed
     * @throws DoesNotFound
     */
    public function post($name, $defVal = null)
    {
        if (isset($_POST[$name])) {
            return $_POST[$name];
        }

        if ($defVal !== null) {
            return $defVal;
        }

        throw new DoesNotFound("POST parameter '$name' not found.");
    }

    /**
     * Check if given POST paramter exists.
     * @param string $name POST parameter name.
     * @return bool
     * @access public
     */
    public function postExists($name)
    {
        return isset($_POST[$name]);
    }

    /**
     * Return COOKIE by it's name.
     * @param string $name Cookie parameter name.
     * @return mixed
     * @throws DoesNotFound
     */
    public function cookie($name)
    {
        if (!isset($_COOKIE[$name])) {
            throw new DoesNotFound("Cookie '$name' not found.");
        }

        return $_COOKIE[$name];
    }

    /**
     * Return FILES parameter by it's name.
     * @param string $name FILES parameter name.
     * @return mixed
     * @throws DoesNotFound
     */
    public function files($name)
    {
        if (!isset($_FILES[$name])) {
            throw new DoesNotFound("FILES parameter '$name' not found.");
        }

        return $_FILES[$name];
    }
}