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
    public $args = [];              // Method arguments
    public $controller;             // Response controller
    public $method = 'index';       // Default method
    public $path;                   // Controller path
    public $response_code = '200';  // HTTP response code
    public $routes = [];            // Routes (defined in $routes[])
    
    /**
    * Get routes and call _set_path() method
    *
    * @param string $url Request url
    *
    * @return void Attribute definition
    */
    public function __construct($url)
    {
        // Get routes
        try {
            // Isset routes file ?
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
        foreach(scandir(CONFIG_PATH) as $route)
        {
            if(preg_match('#_routes.php$#', $route))
            {
                include(CONFIG_PATH . $route);
            }
        }
        
        try {
            // Isset $routes?
            if (!isset($routes))
            {
                throw new IZY_Exception('Tableau $routes non défini dans le fichier ' . CONFIG_PATH . 'routes.php.');
                die;
            }
            // Isset 404 url?
            else if (!isset($routes['404_url']))
            {
                throw new IZY_Exception('$routes[\'404_url\'] non définie dans le fichier ' . CONFIG_PATH . 'routes.php.');
                die;
            }
            // Isset index url?
            else if (!isset($routes['index']))
            {
                throw new IZY_Exception('$routes[\'index\'] non définie dans le fichier ' . CONFIG_PATH . 'routes.php.');
                die;
            }

            $this->routes = $routes;
        }
        catch (IZY_Exception $e)
        {
          echo $e;
        }

        // Index url if url empty
        $url = ($url === '') ? $this->routes['index'] : $url;

        $this->set_path($url);
    }

    /**
    * get route from request
    *
    * @param string $url Input url
    *
    * @return string Route according to request
    */
    private function _get_route($url)
    {
        // Isset literal route ?
        if(isset($this->routes[$url]))
        {
            return $this->routes[$url];
        }

        // Loop on routes
        foreach($this->routes as $key => $val)
        {
            // RegEx match ?
            if(preg_match('#^' . $key . '$#', $url))
            {
                // Do we have a back-reference?
                if(strpos($val, '$') !== FALSE AND strpos($key, '(') !== FALSE)
                {
                    $val = preg_replace('#^' . $key . '$#', $val, $url);
                }
                return $val;
            }
        }

        return $url;
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
        $method = $this->method;

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
            $this->path = $path;
            // Controller
            $this->controller = $controller;
            // Method
            $this->method = $method;
            // Arguments
            if(count($segments) > 2)
            {
                $this->args = array_slice($segments, 2);
                // Path
                $this->path .= implode(DIRECTORY_SEPARATOR, $this->args);
            }
        }
        else if ($_is_literal)
        {
            $_is_literal = false;
            $this->set_path($url);
        }
        else if ($this->response_code != '404')
        {
            $this->response_code = '404';

            if($this->routes['404_url'] != '')
            {
                $this->set_path($this->routes['404_url']);
            }
        }
    }
}
