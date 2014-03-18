<?php

namespace classes\main\http;
/**
 * HTTP клиент
 */
class HttpClient
{
    /**
     * User-agent
     */
    const USER_AGENT = 'RRStudios';

    /**
     * Timeout запроса (в секундах)
     */
    const CONNECT_TIMEOUT = 4;

    /**
     * Клиент
     * @var resource
     */
    protected $_client;

    /**
     * Конструктор
     */
    public function __construct()
    {
        $this->_client = self::_getClient();
    }


    public function post($url, array $params = null, $json = false)
    {
        curl_setopt($this->_client, CURLOPT_POST, 1);
        curl_setopt($this->_client, CURLOPT_URL, $url);
        if ($json) {
            curl_setopt(
                $this->_client,
                CURLOPT_POSTFIELDS,
                $params[0]
            );
        } elseif ($params && count($params)) {
            curl_setopt(
                $this->_client,
                CURLOPT_POSTFIELDS,
                http_build_query($params)
            );
        }

        return $this->_exec();
    }

    public function put($url, $content = null)
    {
        curl_setopt($this->_client, CURLOPT_PUT, 1);
        curl_setopt($this->_client, CURLOPT_INFILE, $content);
        curl_setopt($this->_client, CURLOPT_INFILESIZE, filesize($content));
        curl_setopt($this->_client, CURLOPT_URL, $url);

        return $this->_exec();
    }

    public function get($url, array $params = null, $info = false)
    {
        if ($params && count($params)) {
            $url = $url . '?' . http_build_query($params);
        }
        curl_setopt($this->_client, CURLOPT_URL, $url);

        return $this->_exec($info);
    }

    public function setAuthBase($login, $password)
    {
        curl_setopt($this->_client, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($this->_client, CURLOPT_USERPWD, "{$login}:{$password}");
    }

    public function setHeader($value)
    {
        curl_setopt($this->_client, CURLOPT_HTTPHEADER, array($value));
    }

    protected function _getClient()
    {
        $defaults = array(
            CURLOPT_HEADER => 0,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => self::CONNECT_TIMEOUT,
            CURLOPT_USERAGENT => self::USER_AGENT,
        );

        $client = curl_init();
        curl_setopt_array($client, $defaults);

        return $client;
    }

    protected function _exec($info = false)
    {
        $result = curl_exec($this->_client);
        $error = curl_error($this->_client);
        if (!$result && $error) {
            trigger_error($error);
        }
        if ($info) {
            $status = curl_getinfo($this->_client);
            curl_close($this->_client);

            return $status;
        }
        curl_close($this->_client);

        return $result;
    }
}