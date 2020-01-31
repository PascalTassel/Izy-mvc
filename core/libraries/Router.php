<?php

namespace core\libraries;

if(!defined('IZI')) die('DIRECT ACCESS FORBIDDEN');

/**
* Define path, namespace, controller, method and args
* @author https://www.izi-mvc.com
*/
class IZI_Router
{
  private static $_routes;                             // Routes (set in $routes[])
  private static $_segments;                           // Output url as array
  private static $_path = 'app/controllers/';          // Controller's path
  private static $_namespace;                          // Controller's namespace
  private static $_controller;                         // Controller
  private static $_method = 'index';                   // Default method
  private static $_args = [];                          // Method arguments

  public function __construct($url)
  {
    // Controller namespace
    self::$_namespace = '\\' . str_replace([DIR_PATH, '/'], ['', '\\'], CONTROLLERS_PATH);

    try
    {
      // Include routes
      if(!is_file(CONFIG_PATH . 'routes.php'))
      {
        throw new IZI_Exception('File ' . CONFIG_PATH . 'routes.php not found.');
      }
      include(CONFIG_PATH . 'routes.php');

      if(!isset($routes))
      {
        throw new IZI_Exception('Routes array is undefined.');
      }

      // Custom routes
      foreach(scandir(CONFIG_PATH) as $route)
      {
        if(preg_match('#_routes.php$#', $route))
        {
          include(CONFIG_PATH . $route);
        }
      }

      self::$_routes = $routes;

      // Undefined 404_url route
      if(!isset(self::$_routes['404_url']))
      {
        throw new IZI_Exception('404\'s route undefined.');
      }
      // Default 404_url
      else if(self::$_routes['404_url'] == '')
      {
        self::$_routes['404_url'] = 'core/libraries/controller/show_404';
      }

      // Index url
      if(!isset(self::$_routes['index']))
      {
        throw new IZI_Exception('Index\'s route undefined.');
      }
      // Index page if url empty
      $url = $url == '' ? self::$_routes['index'] : $url;
    }
    catch (IZI_Exception $e)
    {
      die($e);
    }

    self::set_routing($url);
  }

  public static function get_args()
	{
		return self::$_args;
  }

  public static function get_controller()
	{
		return self::$_controller;
  }

  public static function get_method()
	{
		return self::$_method;
  }

  public static function get_namespace()
	{
		return self::$_namespace;
  }

  private static function get_route()
	{
    // Write url
    $url = implode('/', array_filter(self::$_segments));

    // If isset url
		if(isset(self::$_routes[$url]))
		{
      self::$_segments = explode('/', self::$_routes[$url]);
		}
		else
    {
      // Loop on routes
  		foreach(self::$_routes as $key => $val)
  		{
        // RegEx match ?
  			if(preg_match('#^' . $key . '$#', $url))
  			{
  				// Do we have a back-reference?
  				if(strpos($val, '$') !== FALSE AND strpos($key, '(') !== FALSE)
  				{
            $val = preg_replace('#^' . $key . '$#', $val, $url);
  				}
          self::$_segments = explode('/', $val);

  				break;
  			}
      }
    }
	}

  /**
  * Get $_routing
  * @param string $url Input url
  * @return array $_routing
  */
	public static function set_routing($url)
	{
    // Split url
    self::$_segments = array_filter(explode('/', $url));

    // Route ?
    self::get_route();

    // Core controller ?
    if(self::$_segments[0] == 'core')
    {
      self::$_namespace = '\\core\libraries\\';
      self::$_segments = array_slice(self::$_segments, 2);
      self::$_segments[0] = 'IZI_' . ucfirst(self::$_segments[0]);
      self::$_path = 'core/libraries/';
    }
    else
    {
      // Dir ?
      $is_dir = is_dir(CONTROLLERS_PATH . self::$_segments[0]);

      try
      {
        if($is_dir)
        {
          if(count(self::$_segments) == 1)
          {
            throw new IZI_Exception('Controller is not defined.');
          }

          // Namespace
          self::$_namespace .= self::$_segments[0] . '\\';

          // Add to path
          self::$_path .= self::$_segments[0] . '/';

          // update segments
          self::$_segments = array_slice(self::$_segments, 1);
        }
      }
      catch (IZI_Exception $e)
      {
        die($e);
      }
    }

    // Controller
    $_controller = ucfirst(self::$_segments[0]);
    // Add to path
    self::$_path .= $_controller . '/';

    // If controller exists
    if(class_exists(self::$_namespace . $_controller))
    {
      self::$_controller = $_controller;

      // Method ?
      if(count(self::$_segments) >= 2)
      {
        self::$_method = self::$_segments[1];
        // Add to path
        self::$_path .= self::$_method. '/';

        // Arguments
        if(count(self::$_segments) > 2)
        {
          self::$_args = array_slice(self::$_segments, 2);
          // Add to path
          self::$_path .= implode('/', self::$_args);
        }
      }

      // Path
      self::$_path = rtrim(self::$_path, '/');

      // 404 url
      if($url != self::$_routes['404_url'])
      {
        IZI_Output::set_canonical('canonical', \core\helpers\Url_helper::current_url());
      }
    }
    else
    {
      try
      {
        // 404 Controller not found
        if($url == self::$_routes['404_url'])
        {
          throw new IZI_Exception('404 Controller ' . self::$_routes['404_url'] . ' not found.');
        }
      }
      catch (IZI_Exception $e)
      {
        die($e);
      }

      self::set_routing(self::$_routes['404_url']);
    }
	}
}
