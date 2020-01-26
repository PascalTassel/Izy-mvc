<?php

namespace core\libraries;

if(!defined("IZI")) die("DIRECT ACCESS FORBIDDEN");

/**
* Define path, namespace, controller, method and args
* @author https://www.izi-mvc.com
*/
class IZI_Router
{
  private static $_routes;                              // Routes (set in $routes[])
  private static $_segments;                            // Output url as array
  private static $_controller_path = CONTROLLERS_PATH;  // Path to controller
  private static $_namespace;                           // Controller's namespace
  private static $_method = "index";                    // Default method
  private static $_routing;                             // Url routing infos

  public function __construct()
  {
    // Controller namespace
    self::$_namespace = "\\" . str_replace([DIR_PATH, "/"], ["", "\\"], CONTROLLERS_PATH);

    try
    {
      // Include routes
      if(!is_file(CONFIG_PATH . "routes.php"))
      {
        throw new IZI_Exception("File " . CONFIG_PATH . "routes.php not found.");
      }
      include(CONFIG_PATH . "routes.php");

      if(!isset($routes))
      {
        throw new IZI_Exception("Routes array is undefined.");
      }

      // Custom routes
      foreach(scandir(CONFIG_PATH) as $route)
      {
        if(preg_match("#_routes.php$#", $route))
        {
          include(CONFIG_PATH . $route);
        }
      }

      self::$_routes = $routes;

      // Undefined 404_url route
      if(!isset(self::$_routes["404_url"]))
      {
        throw new IZI_Exception("404's route undefined.");
      }
      // Default 404_url
      else if(self::$_routes["404_url"] == "")
      {
        self::$_routes["404_url"] = "core/libraries/IZI_Controller/error_404";
      }

      // Index url
      if(!isset(self::$_routes["index"]))
      {
        throw new IZI_Exception("Index's route undefined.");
      }
    }
    catch (IZI_Exception $e)
    {
      die($e);
    }
  }

  public static function get_controller()
  {
    return self::$_routing["controller"];
  }

  public static function get_method()
  {
    return self::$_routing["method"];
  }

  /**
  * Get route from $_segments
  */
  private static function get_route()
	{
    // Write url
    $url = implode("/", array_filter(self::$_segments));

    // If isset url
		if(isset(self::$_routes[$url]))
		{
      self::$_segments = explode("/", self::$_routes[$url]);
		}
		else
    {
      // Loop on routes
  		foreach(self::$_routes as $key => $val)
  		{
        // RegEx match ?
  			if(preg_match("#^".$key."$#", $url))
  			{
  				// Do we have a back-reference?
  				if(strpos($val, "$") !== FALSE AND strpos($key, "(") !== FALSE)
  				{
            $val = preg_replace("#^".$key."$#", $val, $url);
  				}
          self::$_segments = explode("/", $val);

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
	public static function get_routing($url)
	{
    // Index if undefined
    $url = $url == "" ? self::$_routes["index"] : $url;

    // Split url
    self::$_segments = array_filter(explode("/", $url));

    // Default routing
    self::$_routing = [
      "path" => "",
      "namespace" => self::$_namespace,
      "controller" => null,
      "method" => self::$_method,
      "args" => []
    ];

    // Route ?
    self::get_route();

    // IZI_Controller ?
    if(self::$_segments[0] == "core")
    {
      self::$_controller_path = "core/libraries/";
      self::$_routing["namespace"] = "\\core\libraries\\";
      self::$_segments = array_slice(self::$_segments, 2);
    }
    else
    {
      // Dir ?
      $is_dir = is_dir(self::$_controller_path . self::$_segments[0]);

      try
      {
        if($is_dir)
        {
          if(count(self::$_segments) == 1)
          {
            throw new IZI_Exception("Controller is not defined.");
          }

          // Namespace
          self::$_routing["namespace"] .= self::$_segments[0] . "\\";

          // Define dir and update segments
          self::$_routing["path"] .= self::$_segments[0] ."/";
          self::$_segments = array_slice(self::$_segments, 1);
        }
      }
      catch (IZI_Exception $e)
      {
        die($e);
      }
    }


    // Controller
    $controller = ucfirst(self::$_segments[0]);
    self::$_routing["path"] .= $controller;

    // If controller exists
    if(class_exists(self::$_routing["namespace"] . $controller))
    {
      self::$_routing["controller"] = $controller;

      // Method ?
      if(count(self::$_segments) >= 2)
      {
        self::$_routing["method"] = self::$_segments[1];

        // Arguments
        if(count(self::$_segments) > 2)
        {
          self::$_routing["args"] = array_slice(self::$_segments, 2);
        }
      }

      // 404 url
      if($url == self::$_routes["404_url"])
      {
        // 404 header
        IZI_Http::set_code();
      }
      else
      {
        IZI_Output::set_canonical("canonical", \core\helpers\Url_helper::current_url());
      }
    }
    else
    {
      try
      {
        // 404 Controller not found
        if($url == self::$_routes["404_url"])
        {
          throw new IZI_Exception("404 Controller " . self::$_controller_path . self::$_routing["path"] . " not found.");
        }
      }
      catch (IZI_Exception $e)
      {
        die($e);
      }

      self::get_routing(self::$_routes["404_url"]);
    }

    return self::$_routing;
	}
}
