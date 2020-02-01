<?php

namespace core\libraries;

if(!defined('IZI')) die('DIRECT ACCESS FORBIDDEN');

/**
* Load a hook, a controller or a view
* @author https://www.izi-mvc.com
*/
class IZI_Load
{
	/**
  * Load controller
  * @param string $path Controller path
  * @param boolean $hooks Enable hooks
  */
	public static function controller()
	{
		// Get url
		$url = IZI_Url::get_url();

		// Router instance
		$router = new IZI_Router($url);

		$namespace = $router::get_namespace();
		$class = $namespace . $router::get_controller();
		$method = $router::get_method();
		$args = $router::get_args();

    // Extended IZI controller ?
    if($class == '\core\libraries\IZI_Controller')
    {
			$extended_class = str_replace([DIR_PATH, '/'], ['', '\\'], APP_PATH) . 'core\Controller';
      if(class_exists($extended_class))
      {
				$class = $extended_class;
      }
    }

		// Controller instance
		$controller = new $class();

		try
    {
			// Call method
			if(!in_array($method, get_class_methods($controller)))
			{
        throw new IZI_Exception('Method ' . $method . ' not found in controller ' . $class . '.');
      }
			// Pre_controller hook
			self::hook('pre_controller');

  		call_user_func_array(array($controller, $method), $args);

			// Unset router
			unset($router);

		  // Unset controller
		  unset($controller);

			// Post_controller hook
			self::hook('post_controller');
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
				if(!is_file(VIEWS_PATH . $view . '.php'))
				{
	        throw new IZI_Exception('View ' . VIEWS_PATH . $view . '.php not found.');
	      }
	      // Launch cache
	      ob_start();

				// Pre_view hook
				self::hook('pre_view');

	      include(VIEWS_PATH . $view . '.php');

				// Post_view hook
				self::hook('post_view');

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
		if(isset(CONFIG['hooks'][$hook_name]))
		{
			$hook = CONFIG['hooks'][$hook_name];

			try
	    {
				// Instanciate hook
				if(!isset($hook['class']) || empty($hook['class']))
				{
	        throw new IZI_Exception('Hook ' . $hook_name . ' : \'class\' key  not exists.');
	      }

				$class = '\\' . $hook['class'];
				if(!class_exists($class))
				{
	        throw new IZI_Exception('Hook Class ' . $class . ' not exists.');
	      }
				$controller = new $class();
			}
	    catch (IZI_Exception $e)
	    {
	      die($e);
	    }

			try
	    {
				// Call method
				$method = $hook['method'];
				if(!in_array($method, get_class_methods($controller)))
				{
	        throw new IZI_Exception('Method ' . $method . 'not found in ' . $class . '.');
	      }
				call_user_func_array(array($controller, $method), []);

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
