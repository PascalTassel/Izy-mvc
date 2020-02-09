<?php

namespace core\system;

if(!defined('IZY')) die('DIRECT ACCESS FORBIDDEN');

/**
* Load a hook, a controller or a view
* @author https://www.izi-mvc.com
*/
class IZY_Hooks
{
	private static $_hooks;	// Hooks (set in app/config/config.php)

	/**
	* Exec a hook
	* @param string $hook_name Hook name
	*/
	public function __construct()
	{
		self::$_hooks = !is_null(get_config('hooks')) && gettype(get_config('hooks')) == 'array' ? get_config('hooks') : [];
	}

	public function set_hook($hook)
	{
		if(isset(self::$_hooks[$hook]))
		{
			$hook = self::$_hooks[$hook];
			$class = '\\' . $hook['class'];
			$method = $hook['method'];
			$args = isset($hook['args']) && (gettype($hook['args']) == 'array') ? $hook['args'] : [];

			if(class_exists($class))
			{
				$controller = new $class();

				// Call method
				if(in_array($method, get_class_methods($controller)))
				{
					call_user_func_array(array($controller, $method), $args);
				}

				// Unset hook
				unset($controller);
	    }
		}
	}
}
