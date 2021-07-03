<?php
namespace core\system;

if(!defined('IZY')) die('DIRECT ACCESS FORBIDDEN');

/**
* Set HTTP headers response
*
* @package Izy-mvc
* @copyright 2021 © Pascal Tassel for https://www.izy-mvc.com <contact[@]izy-mvc.com>
*/
class IZY_Http
{
    private $_headers = [];
    private $_codes = array(    // HTTP codes used for headers
        '200' => 'OK',
        '201' => 'Created',
        '202' => 'Accepted',
        '203' => 'Non-Authoritative Information',
        '204' => 'No Content',
        '205' => 'Reset Content',
        '206' => 'Partial Content',

        '300' => 'Multiple Choices',
        '301' => 'Moved Permanently',
        '302' => 'Found',
        '304' => 'Not Modified',
        '305' => 'Use Proxy',
        '307' => 'Temporary Redirect',

        '400' => 'Bad Request',
        '401' => 'Unauthorized',
        '403' => 'Forbidden',
        '404' => 'Not Found',
        '405' => 'Method Not Allowed',
        '406' => 'Not Acceptable',
        '407' => 'Proxy Authentication Required',
        '408' => 'Request Timeout',
        '409' => 'Conflict',
        '410' => 'Gone',
        '411' => 'Length Required',
        '412' => 'Precondition Failed',
        '413' => 'Request Entity Too Large',
        '414' => 'Request-URI Too Long',
        '415' => 'Unsupported Media Type',
        '416' => 'Requested Range Not Satisfiable',
        '417' => 'Expectation Failed',

        '500' => 'Internal Server Error',
        '501' => 'Not Implemented',
        '502' => 'Bad Gateway',
        '503' => 'Service Unavailable',
        '504' => 'Gateway Timeout',
        '505' => 'HTTP Version Not Supported'
    );
    
    /**
    * Add HTTP header to response
    *
    * @param string $header Header to add
    *
    * @return void Adding Header to $_headers array()
    */
    public function add_header($header)
    {
        array_push($this->_headers, $header);
    }

    /**
    * Set header location
    * @param string $url Url location
    */
    
    /**
    * Set header location
    *
    * @param string $url Url to redirect
    *
    * @throws IZY_Exception
    *
    * @return function Header location
    */
    public function location($url = '')
    {
        try {
            // Url isn't string ?
            if (gettype($url) !== 'string')
            {
                throw new IZY_Exception('L\'url de redirection doit être une chaîne de caractères.', 1);
                die;
            }
            // Url is empty ?
            else if (trim($url) === '')
            {
                throw new IZY_Exception('L\'url de redirection indiquée est vide.');
                die;
            }

            header('Location: ' . trim($url));
            die;
        }
        catch (IZY_Exception $e)
        {
          echo $e;
        }
    }
    
    /**
    * Redirection
    *
    * @param string $url Url to redirect
    * @param boolean $replace Replace or add to headers stack
    * @param int $code
    *
    * @throws IZY_Exception
    *
    * @return function Header location
    */
    public function redirect($url, $replace = TRUE, $code = 302)
    {
        try {
            // Url isn't string ?
            if (gettype($url) !== 'string')
            {
                throw new IZY_Exception('L\'url de redirection doit être une chaîne de caractères.');
                die;
            }
            // Url is empty ?
            else if (trim($url) === '')
            {
                throw new IZY_Exception('L\'url de redirection indiquée est vide.');
                die;
            }
            // replace isn't boolean ?
            else if (gettype($replace) !== 'boolean')
            {
                throw new IZY_Exception('Le second paramètre de la méthode IZY_Http->redirect() doit être de type booléen.');
                die;
            }

            header('Location: ' . $url, $replace, $code);
            die;
        }
        catch (IZY_Exception $e)
        {
          echo $e;
        }
    }
    
    /**
    * Add headers to response
    *
    * @return mixed Header
    */
    public function send_headers()
    {
        foreach ($this->_headers as $header)
        {
            header($header);
        }
    }

    /**
    * Set HTTP header response code
    *
    * @param string $code Header code
    */
    public function response_code($code = '404')
    {
        try {
            // Unknown code?
            if (!isset($this->_codes[$code]))
            {
                throw new IZY_Exception('Code HTTP/1.1. <strong>' . $code . '</strong> inconnu.');
                die;
            }
            
            // Add header
            header('HTTP/1.1 ' . $code . ' ' . $this->_codes[$code]);
        }
        catch (IZY_Exception $e)
        {
          echo $e;
        }
    }
}
