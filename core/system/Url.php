<?php
namespace core\system;

if(!defined('IZY')) die('DIRECT ACCESS FORBIDDEN');

/**
* This file is part of the Izy_mvc project.
*
* Sanitize the request
*
* @package Izy-mvc
* @copyright 2021 © Pascal Tassel for https://www.izy-mvc.com <contact[@]izy-mvc.com>
*/

/**
* Set request URL and queries
*
* @author Pascal Tassel : https://www.izy-mvc.com
*/
class IZY_Url
{
    private $_host;           // Host
    private $_protocol;       // Protocol
    private $_queries = [];   // Query parameters as associative array
    private $_request = '';   // Request
    
    /**
    * Call _set_uri() method
    *
    * @param string $item Name of the parameter
    *
    * @return object IZY_Yrl instance
    */
    public function __construct()
    {
        $this->_set_uri();
    }
    
    /**
    * Get host
    *
    * @return string Host name
    */
    public function get_host()
    {
        return $this->_host;
    }
    
    /**
    * Set host
    *
    * @param string $host Hostname
    *
    * @throws IZY_Exception
    *
    * @return void
    */
    public function set_host($host)
    {
        try {
            if (gettype($host) !== 'string')
            {
                throw new IZY_Exception('Le paramètre de la méthode IZY_Url->set_host() doit être une chaîne de caractères.');
                die;
            }
            $this->_host = $host;
        }
        catch (IZY_Exception $e)
        {
          echo $e;
        }
    }
    
    /**
    * Get protocol
    *
    * @return string Protocol
    */
    public function get_protocol()
    {
        return $this->_protocol;
    }
    
    /**
    * Set protocol
    *
    * @param string $protocol Protocol url
    *
    * @throws IZY_Exception
    *
    * @return void
    */
    public function set_protocol($protocol)
    {
        try {
            if (gettype($protocol) !== 'string')
            {
                throw new IZY_Exception('Le paramètre de la méthode IZY_Url->set_protocol() doit être une chaîne de caractères.');
                die;
            }
            $this->_protocol = $protocol;
        }
        catch (IZY_Exception $e)
        {
          echo $e;
        }
    }
    
    /**
    * Get queries
    *
    * @return array Query string as associative array
    */
    public function get_queries()
    {
        return $this->_queries;
    }
    
    /**
    * Set queries
    *
    * @param array $queries Associative array
    *
    * @throws IZY_Exception
    *
    * @return void
    */
    public function set_queries($queries)
    {
        try {
            if (gettype($queries) !== 'array')
            {
                throw new IZY_Exception('Le paramètre de la méthode IZY_Url->set_queries() doit être un tableau associatif.');
                die;
            }
            $this->_queries = $queries;
        }
        catch (IZY_Exception $e)
        {
          echo $e;
        }
    }
    
    /**
    * Get request
    *
    * @return string Request
    */
    public function get_request()
    {
        return $this->_request;
    }
    
    /**
    * Set request
    *
    * @param string $request Protocol url
    *
    * @throws IZY_Exception
    *
    * @return void
    */
    public function set_request($request)
    {
        try {
            if (gettype($request) !== 'string')
            {
                throw new IZY_Exception('Le paramètre de la méthode IZY_Url->set_request() doit être une chaîne de caractères.');
                die;
            }
            $this->_request = $request;
        }
        catch (IZY_Exception $e)
        {
          echo $e;
        }
    }
    
    /**
    * Get request queries as query string
    *
    * @return string Request query string
    */
    public function query_string()
    {
        return http_build_query($this->_queries);
    }
    
    /**
    * Extract protocol, host, request and queries from request
    *
    * @throws IZY_Exception
    *
    * @return void Attributes definition
    */
    private function _set_uri()
    {
        // Set protocol
        $this->_protocol = $_SERVER['REQUEST_SCHEME'] != '' ? $_SERVER['REQUEST_SCHEME'] : 'http';
        if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on'))
        {
            $this->_protocol = 'https';
        }

        // Set host
        try {
            if (is_null(get_config('host')) || empty(get_config('host')))
            {
                throw new IZY_Exception('$config[\'host\'] non définie dans le fichier ' . CONFIG_PATH . 'config.php.');
                die;
            }
            $this->_host = strtolower(get_config('host'));
        }
        catch (IZY_Exception $e)
        {
          echo $e;
        }

        $uri = ltrim(str_replace($this->_host, '', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']), '/');

        // Write request
        $this->_request = strtolower(parse_url($uri, PHP_URL_PATH));

        // Set queries parameters
        $query_string = strtolower(parse_url($uri, PHP_URL_QUERY));
        parse_str($query_string, $this->_queries);
    }
}
