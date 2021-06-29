<?php
namespace core\system;

if(!defined('IZY')) die('DIRECT ACCESS FORBIDDEN');

/**
* Get URL and queries
* @author Pascal Tassel : https://www.izy-mvc.com
*/
class IZY_Url
{
    public $protocol;               // Protocol
    public $host;                   // Host
    public $request = '';           // Request
    public $queries = [];           // Query string as array

    public function __construct()
    {
        $this->_set_uri();
    }

    public function query_string()
    {
        return http_build_query($this->queries);
    }

    private function _set_uri()
    {
        // Protocol
        $this->protocol = $_SERVER['REQUEST_SCHEME'] != '' ? $_SERVER['REQUEST_SCHEME'] : 'http';
        if(isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on'))
        {
            $this->protocol = 'https';
        }

        // Host
        try {
            if(is_null(get_config('host')) || empty(get_config('host')))
            {
                throw new IZY_Exception('$config[\'host\'] non définie dans le fichier ' . CONFIG_PATH . 'config.php.');
                die;
            }
            $this->host = strtolower(get_config('host'));
        }
        catch (IZY_Exception $e)
        {
          echo $e;
        }

        $uri = ltrim(str_replace($this->host, '', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']), '/');

        // Request
        $this->request = strtolower(parse_url($uri, PHP_URL_PATH));

        // Queries
        $query_string = strtolower(parse_url($uri, PHP_URL_QUERY));
        parse_str($query_string, $this->queries);
    }
}
