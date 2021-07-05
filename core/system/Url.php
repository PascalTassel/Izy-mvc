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
    private $_host = '';           // Host
    private $_protocol = '';       // Protocol
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
    * Get and set attributes
    *
    * @param string $method Function name
    * @param string $value Function name
    *
    * @return mixed|void
    */
    public function __call($method, $value)
    {
        $attribute = '_' . lcfirst(substr($method, 4));
        
        // Is it a valid attribute
        if (property_exists($this, $attribute))
        {
            // Getter
            if (strncasecmp($method, 'get_', 4) === 0)
            {
                return $this->$attribute;
                
            // Setter
            } else if (strncasecmp($method, 'set_', 4) === 0) {
                
                // Is it same type
                if (gettype($value) === gettype($this->$attribute))
                {
                    $this->$attribute = $value;
                }
            }
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
