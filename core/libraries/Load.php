<?php

namespace core\libraries;

if(!defined("IZI")) die("DIRECT ACCESS FORBIDDEN");

/**
* Load a hook, a controller or a view
* @author https://www.izi-mvc.com
*/
class IZI_Load
{
	/**
  * Load a controller
  * @param string $path Controller path
  * @param boolean $hooks Enable hooks
  */
	public static function controller($url, $hooks = false)
	{
		// Router instance
		$router = new IZI_Router();

		// Get routing
		$routing = $router::get_routing($url);

		unset($router);

		// Pre_controller hook
		if($hooks)
		{
			self::hook("pre_controller");
		}

		// Instanciate controller
		if($routing["controller"] == "IZI_Controller")
		{
			$controller = new IZI_Controller();
		}
		else
		{
			$class = $routing["namespace"] . $routing["controller"];
			$controller = new $class();
		}

		try
    {
			// Call method
			if(!in_array($routing["method"], get_class_methods($controller)))
			{
        throw new IZI_Exception("Method {$routing["method"]} not found in controller {$class}.");
      }
  		call_user_func_array(array($controller, $routing["method"]), $routing["args"]);

		  // Unset controller
		  unset($controller);

			// Post_controller hook
			if($hooks)
			{
				self::hook("post_controller");
			}
    }
    catch (IZI_Exception $e)
    {
      die($e);
    }
	}

	/**
	* Get HTML view
	* @param string $view View name
	* @param array $datas Datas view
	*/
	public static function view($view, $datas)
  {
      // Convert datas array keys to vars
      if(!empty($datas))
      {
        extract($datas);
      }

			try
	    {
				// Include view
				if(!is_file(VIEWS_PATH . $view . ".php"))
				{
	        throw new IZI_Exception("File " . VIEWS_PATH . "{$view}.php not found.");
	      }
	      // Launch cache
	      ob_start();

	      include(VIEWS_PATH . $view . ".php");

	      // Content view
	      $content = ob_get_contents();

				// Fermeture du cache
				ob_end_clean();

				return $content;
			}
	    catch (IZI_Exception $e)
	    {
	      die($e);
	    }
	}

	/**
	* Exec a hook
	* @param string $hook_name Hook name
	*/
	public static function hook($hook_name)
	{
		if(isset(CONFIG["hooks"][$hook_name]))
		{
			$hook = CONFIG["hooks"][$hook_name];

			try
	    {
				// Instanciate hook
				if(!isset($hook["class"]) || empty($hook["class"]))
				{
	        throw new IZI_Exception("Hook {$hook_name} not defined.");
	      }

				if(!class_exists($hook["class"]))
				{
	        throw new IZI_Exception("Hook Class {$hook["class"]} not exists.");
	      }
				$class = $hook["class"];
				$controller = new $class();
			}
	    catch (IZI_Exception $e)
	    {
	      die($e);
	    }

			try
	    {
				// Call method
				$method = $hook["method"];
				if(!in_array($method, get_class_methods($controller)))
				{
	        throw new IZI_Exception("Method {$method} not found in {$class}.");
	      }
				$args = isset($hook["args"]) && (gettype($hook["args"]) == "array") ? $hook["args"] : [];
				call_user_func_array(array($controller, $method), $args);

				// Unset hook
				unset($controller);
			}
	    catch (IZI_Exception $e)
	    {
	      die($e);
	    }
		}
	}
}
