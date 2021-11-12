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
    private static $_args = [];              // Method arguments
    private static $_controller = '';        // Response controller
    private static $_method = 'index';       // Default method
    private static $_path = '';              // Controller path
    private static $_response_code = '200';  // HTTP response code
    private static $_routes = [];            // Routes (defined in $routes[])
    
    /**
    * Get routes and call _set_response() method
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

            self::$_routes = $routes;
        }
        catch (IZY_Exception $e)
        {
          echo $e;
        }

        // Index url if url empty
        $url = ($url === '') ? self::$_routes['index'] : $url;

        $this->set_response($url);
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
                return self::${$attribute};
                
            // Setter
            } else if (strncasecmp($method, 'set_', 4) === 0) {

                // Get value
                $value = $value[0];
                
                // Is it same type
                if (gettype($value) === gettype(self::${$attribute}))
                {
                    self::${$attribute} = $value;
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
    public function set_response($url)
    {
        static $_is_literal = true;
        
        if ($_is_literal) {
            $route = $url;
        } else {
            $route = self::_get_route($url);
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
        $method = self::$_method;

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

        try {
            if (class_exists($controller) && in_array($method, get_class_methods($controller)))
            {
                // Path
                self::$_path = $path;
                // Controller
                self::$_controller = $controller;
                // Method
                self::$_method = $method;
                // Arguments
                if(count($segments) > 2)
                {
                    self::$_args = array_slice($segments, 2);
                    // Path
                    self::$_path .= implode(DIRECTORY_SEPARATOR, self::$_args);
                }
            }
            else if ($_is_literal)
            {
                $_is_literal = false;
                $this->set_response($url);
            }
            else if (self::$_response_code != '404')
            {
                self::$_response_code = '404';

                if(self::$_routes['404_url'] != '')
                {
                    $this->set_response(self::$_routes['404_url']);
                }
            }
            else {
                throw new IZY_Exception('Route 404 introuvable : ' . $url);
                die;
            }
        }
        catch (IZY_Exception $e)
        {
          echo $e;
        }
    }

    /**
    * Get route from request
    *
    * @param string $url Input url
    *
    * @return string Route according to request
    */
    private static function _get_route($url)
    {
        // Isset literal route ?
        if (isset(self::$_routes[$url]))
        {
            return self::$_routes[$url];
        }

        // Loop on routes
        foreach (self::$_routes as $key => $val)
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
