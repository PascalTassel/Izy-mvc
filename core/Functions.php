<?php

if(!defined('IZY')) die('DIRECT ACCESS FORBIDDEN');

// Get Config item
if(!function_exists('get_config'))
{
	function get_config($item = '')
	{
		$config =& get_config_files();

		return isset($config[$item])
			? $config[$item]
			: NULL;
	}
}

// Get Config files
if(!function_exists('get_config_files'))
{
	function &get_config_files()
	{
		static $_config;

		if(empty($_config))
		{
		  if(!is_file(CONFIG_PATH . 'config.php'))
		  {
		    echo 'Unable to locate ' . CONFIG_PATH . 'config.php.';
				die;
		  }

			include(CONFIG_PATH . 'config.php');

		  if(!isset($config))
		  {
		    echo 'Unable to locate $config in ' . CONFIG_PATH . 'config.php.';
				die;
		  }

		  // Customs config
		  foreach(scandir(CONFIG_PATH) as $file)
		  {
		    if(preg_match('#_config.php$#', $file))
		    {
		      include(CONFIG_PATH . $file);
		    }
		  }

			$_config = $config;
		}

		return $_config;
	}
}

// Get IZY_Controller instance
if(!function_exists('get_instance'))
{
	function &get_instance()
	{
		$controller =& load_class('Controller');
	  return $controller::get_instance();
	}
}

// Load a [library | system | helper] class (can be extended)
if(!function_exists('load_class'))
{
	function &load_class($class, $directory = 'system', $param = NULL)
	{
		static $_classes = [];

		if(isset($_classes[$class]))
		{
			return $_classes[$class];
		}

		// Look for the class first in the local application folders
		// then in the native system/folders
		$name = FALSE;
		$namespaces = [
			APP_PATH => str_replace([DIR_PATH, '/'], ['', '\\'], APP_PATH) . $directory . '\\',
			DIR_PATH . 'core/' => 'core\\' . $directory . '\\'
		];

		// Look for the class first in the local application/libraries folder
		// then in the native system/libraries folder
		foreach($namespaces as $path => $namespace)
		{
			if(file_exists($path . $directory . '/' . $class . '.php'))
			{
				if(class_exists($namespace . 'IZY_' . $class))
				{
					$name = $namespace . 'IZY_' . $class;
					break;
				}
			}
		}
		
		// Is the request a class extension? If so we load it too
		if($name !== FALSE)
		{
			if(file_exists(APP_PATH . $directory . '/MY_' . $class . '.php') && class_exists($namespaces[APP_PATH] . 'MY_' . $class))
			{
				$name = $namespaces[APP_PATH] . 'MY_' . $class;
			}
		}
		// Load the specified class
		else if(file_exists(APP_PATH . $directory . '/' . $class . '.php') && class_exists($namespaces[APP_PATH] . $class))
		{
			$name = $namespaces[APP_PATH] . $class;
		}

		// Did we find the class?
		if($name === FALSE)
		{
			// Note: We use exit() rather than show_error() in order to avoid a
			// self-referencing loop with the Exceptions class
			//set_status_header(503);
			echo 'Unable to locate the specified class: ' . $class . '.php';
			exit(5); // EXIT_UNK_CLASS
		}

		// Keep track of class loaded
		system_loaded($directory, $class);

		$_classes[$class] = isset($param)
			? new $name($param)
			: new $name();

		return $_classes[$class];
	}
}

// Load a model class
if(!function_exists('load_model'))
{
	function &load_model($model)
	{
		static $_models = [];

		if(isset($_models[$model]))
		{
			return $_models[$model];
		}

		$name = FALSE;

		$namespace = str_replace([DIR_PATH, '/'], ['', '\\'], MODELS_PATH);
		$class = $model;

    // Dir ?
		$segments = explode('/', $model);
		if(count($segments) > 1)
		{
			$class = end($segments);
			$pop = array_pop($segments);
	    $namespace .= implode('\\', $segments) . '\\';
		}

		$class = $namespace . $class;

		// Look for the model in app/model/ dir
		if(file_exists(MODELS_PATH . $model . '.php') && class_exists($class))
		{
			$name = $class;
		}

		// Did we find the model?
		if($name === FALSE)
		{
			echo 'Unable to locate the specified model: ' . $model . '.php';
			exit(5); // EXIT_UNK_CLASS
		}

		// Keep track of model loaded
		models_loaded($model);

		$_models[$model] = new $name();

		return $_models[$model];
	}
}

if ( ! function_exists('models_loaded'))
{
	/**
	 * Keeps track of which libraries have been loaded. This function is
	 * called by the load_class() function above
	 *
	 * @param	string
	 * @return	array
	 */
	function &models_loaded($model = '')
	{
		static $_is_loaded = [];

		if($model != '')
		{
			$_is_loaded[str_replace('/', '_', strtolower($model))] = $model;
		}

		return $_is_loaded;
	}
}

if ( ! function_exists('system_loaded'))
{
	/**
	 * Keeps track of which libraries have been loaded. This function is
	 * called by the load_class() function above
	 *
	 * @param	string
	 * @return	array
	 */
	function &system_loaded($directory, $class = '')
	{
		static $_is_loaded = [
			'helpers' => [],
			'libraries' => [],
			'system' => []
		];

		if($class != '')
		{
			$_is_loaded[$directory][strtolower($class)] = $class;
		}

		return $_is_loaded[$directory];
	}
}

if( ! function_exists('show_404'))
{
	function show_404()
	{
		// 404 header
		get_instance()->http->response_code('404');

    $routes = get_instance()->router->routes;

		if($routes['404_url'] != '')
		{
			get_instance()->router->set_path($routes['404_url']);

			$controller_404 = get_instance()->router->controller;

			if(!empty($controller_404))
			{
				// Empty current output
				get_instance()->output->empty();

				// Call controller
				$class = new $controller_404();
				call_user_func_array(array($class, get_instance()->router->method), get_instance()->router->args);

			  // Unset controller
			  unset($class);

				// Output
				get_instance()->output->_display();
			}
		}

		die;
	}
}

if( ! function_exists('view'))
{
	function view($path, $datas = [])
	{
    // Convert datas array keys to vars
    if(!empty($datas))
    {
      extract($datas);
    }

		// Include view
		if(!is_file(VIEWS_PATH . $path . '.php'))
		{
      echo 'View ' . VIEWS_PATH . $path . '.php not found.';
			die;
    }
    // Launch cache
    ob_start();

		// Pre_view hook
		//self::hook('pre_view');

    include(VIEWS_PATH . $path . '.php');

		// Post_view hook
		//self::hook('post_view');

    // Content view
    $content = ob_get_contents();

		// Fermeture du cache
		ob_end_clean();

		return $content;
	}
}
