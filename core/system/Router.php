<?php

namespace core\system;

if(!defined('IZY')) die('DIRECT ACCESS FORBIDDEN');

/**
* Define path, namespace, controller, method and args
* @author https://www.izi-mvc.com
*/
class IZY_Router
{
  public $routes;                             // Routes (set in app/config/routes.php)
  public $path;                               // Controller path
  public $controller;                         // Controller name
  public $method = 'index';                   // Default method
  public $args = [];                          // Method arguments
  public $response_code = '200';              // Route response code

  public function __construct($url)
  {
    // Routes
    if(!is_file(CONFIG_PATH . 'routes.php'))
    {
      echo 'Unable to locate ' . CONFIG_PATH . 'routes.php.';
      die;
    }
    include(CONFIG_PATH . 'routes.php');

    if(!isset($routes))
    {
      echo 'Unable to locate $routes in' . CONFIG_PATH . 'routes.php.';
      die;
    }

    // Custom routes
    foreach(scandir(CONFIG_PATH) as $route)
    {
      if(preg_match('#_routes.php$#', $route))
      {
        include(CONFIG_PATH . $route);
      }
    }

    // Undefined 404_url route
    if(!isset($routes['404_url']))
    {
      echo '$routes[\'404_url\'] is undefined.';
      die;
    }

    // Index url
    if(!isset($routes['index']))
    {
      echo '$routes[\'index\'] is undefined.';
      die;
    }

    $this->routes = $routes;

    // Index url if url empty
    $url = $url == '' ? $this->routes['index'] : $url;

    $this->set_path($url);
  }

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
  * Get $_routing
  * @param string $url Input url
  * @return array $_routing
  */
	public function set_path($url)
	{
    $route = $this->_get_route($url);

    // Namespace
    $namespace = '\\' . str_replace([DIR_PATH, '/'], ['', '\\'], CONTROLLERS_PATH);
    // Path
    $path = ltrim(str_replace('\\', '/', $namespace), '/');
    // Segments
    $segments = array_filter(explode('/', $route));
    // Controller
    $controller = '';
    // Method
    $method = $this->method;

    // Dir ?
    $is_dir = is_dir(CONTROLLERS_PATH . $segments[0]);
    if($is_dir)
    {
      // Namespace
      $namespace .= $segments[0] . '\\';
      // Path
      $path .= $segments[0] . '/';
      // Segments
      $segments = array_slice($segments, 1);
    }

    if(count($segments) > 0)
    {
      // Path
      $path .= $controller . '/';
      // Controller
      $controller = $namespace . ucfirst($segments[0]);

      if(count($segments) >= 2)
      {
        // Method
        $method = $segments[1];

        // Path
        $path .= $method . '/';
      }
    }

    if(class_exists($controller) && in_array($method, get_class_methods($controller)))
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
        $this->path .= rtrim(implode('/', $this->args));
      }
    }
    elseif($this->response_code != '404')
    {
      $this->response_code = '404';

      if($this->routes['404_url'] != '')
      {
        $this->set_path($this->routes['404_url']);
      }
    }
	}
}
