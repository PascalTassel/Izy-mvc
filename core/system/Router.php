<?php
namespace core\system;

if(!defined('IZY')) die('DIRECT ACCESS FORBIDDEN');

/**
* Parse the request
*
* @package Izy-mvc
* @copyright 2021 © Pascal Tassel for https://www.izy-mvc.com <contact[@]izy-mvc.com>
*/
class IZY_Router
{
    private $_args = [];              // Method arguments
    private $_controller = '';        // Response controller
    private $_method = 'index';       // Default method
    private $_path = '';              // Controller path
    private $_response_code = '200';  // HTTP response code
    private $_routes = [];            // Routes (defined in $routes[])
    
    /**
    * Get routes and call _set_path() method
    *
    * @param string $url Request url
    *
    * @throws IZY_Exception
    *
    * @return void Attribute definition
    */
    public function __construct($url = '')
    {
        // Get routes
        try {
            // Not isset routes file ?
            if (!is_file(CONFIG_PATH . 'routes.php'))
            {
                throw new IZY_Exception('Fichier ' . CONFIG_PATH . 'routes.php introuvable.');
                die;
            }
            
            // Include routes file
            include(CONFIG_PATH . 'routes.php');
        }
        catch (IZY_Exception $e)
        {
          echo $e;
        }

        // Get custom routes
        foreach (scandir(CONFIG_PATH) as $route)
        {
            if (preg_match('#_routes.php$#', $route))
            {
                include(CONFIG_PATH . $route);
            }
        }
        
        try {
            // Not isset $routes?
            if (!isset($routes))
            {
                throw new IZY_Exception('Tableau $routes non défini dans le fichier ' . CONFIG_PATH . 'routes.php.');
                die;
            }
            // Not isset 404 url?
            else if (!isset($routes['404_url']))
            {
                throw new IZY_Exception('$routes[\'404_url\'] non définie dans le fichier ' . CONFIG_PATH . 'routes.php.');
                die;
            }
            // Not isset index url?
            else if (!isset($routes['index']))
            {
                throw new IZY_Exception('$routes[\'index\'] non définie dans le fichier ' . CONFIG_PATH . 'routes.php.');
                die;
            }

            $this->_routes = $routes;
        }
        catch (IZY_Exception $e)
        {
          echo $e;
        }

        // Index url if url empty
        $url = ($url === '') ? $this->_routes['index'] : $url;

        $this->set_path($url);
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
        $attribute = '_' . substr($method, 4);
        
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
    * Extract path, controller, method and args from request
    * Set HTTP Header response code (200 | 404)
    *
    * @param string $url Request
    *
    * @return string Route according to request
    */
    public function set_path($url)
    {
        static $_is_literal = true;
        
        if ($_is_literal) {
            $route = $url;
        } else {
            $route = $this->_get_route($url);
        }

        // Namespace
        $namespace = '\\' . str_replace([DIR_PATH, DIRECTORY_SEPARATOR], ['', '\\'], CONTROLLERS_PATH);
        // Path
        $path = ltrim(str_replace('\\', DIRECTORY_SEPARATOR, $namespace), '/');
        // Segments
        $segments = explode('/', $route);
        // Controller
        $controller = '';
        // Method
        $method = $this->_method;

        // Dir?
        $is_dir = is_dir(CONTROLLERS_PATH . $segments[0]);
        if ($is_dir)
        {
            // Namespace
            $namespace .= $segments[0] . '\\';
            // Path
            $path .= $segments[0] . DIRECTORY_SEPARATOR;
            // Segments
            $segments = array_slice($segments, 1);
        }
        
        if (count($segments) > 0)
        {
            // Path
            $path .= $controller . DIRECTORY_SEPARATOR;
            // Controller
            $controller = $namespace . ucfirst($segments[0]);

            if (count($segments) >= 2)
            {
                // Method
                $method = $segments[1];

                // Path
                $path .= $method . DIRECTORY_SEPARATOR;
            }
        }

        if (class_exists($controller) && in_array($method, get_class_methods($controller)))
        {
            // Path
            $this->_path = $path;
            // Controller
            $this->_controller = $controller;
            // Method
            $this->_method = $method;
            // Arguments
            if(count($segments) > 2)
            {
                $this->_args = array_slice($segments, 2);
                // Path
                $this->_path .= implode(DIRECTORY_SEPARATOR, $this->_args);
            }
        }
        else if ($_is_literal)
        {
            $_is_literal = false;
            $this->set_path($url);
        }
        else if ($this->_response_code != '404')
        {
            $this->_response_code = '404';

            if($this->_routes['404_url'] != '')
            {
                $this->set_path($this->_routes['404_url']);
            }
        }
    }

    /**
    * Get route from request
    *
    * @param string $url Input url
    *
    * @return string Route according to request
    */
    private function _get_route($url)
    {
        // Isset literal route ?
        if (isset($this->_routes[$url]))
        {
            return $this->_routes[$url];
        }

        // Loop on routes
        foreach ($this->_routes as $key => $val)
        {
            // RegEx match ?
            if (preg_match('#^' . $key . '$#', $url))
            {
                // Do we have a back-reference?
                if (strpos($val, '$') !== FALSE AND strpos($key, '(') !== FALSE)
                {
                    $val = preg_replace('#^' . $key . '$#', $val, $url);
                }
                return $val;
            }
        }

        return $url;
    }
}
