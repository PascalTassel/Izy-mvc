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
            try {
                // Isset config file ?
                if (!is_file(CONFIG_PATH . 'config.php'))
                {
                    throw new \core\system\IZY_Exception('Fichier ' . CONFIG_PATH . 'config.php introuvable.');
                    die;
                }
                
                // Include config file
                include(CONFIG_PATH . 'config.php');
            }
            catch (\core\system\IZY_Exception $e)
            {
              echo $e;
            }

            // Include customs config files
            foreach (scandir(CONFIG_PATH) as $file)
            {
                if (preg_match('#_config.php$#', $file))
                {
                    include(CONFIG_PATH . $file);
                }
            }
            
            try {
                // Isset $config ?
                if (!isset($config))
                {
                    throw new \core\system\IZY_Exception('Tableau $config non défini dans le fichier ' . CONFIG_PATH . 'config.php.');
                    die;
                }
                
                $_config = $config;
            }
            catch (\core\system\IZY_Exception $e)
            {
              echo $e;
            }
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
            APP_PATH => str_replace([DIR_PATH, DIRECTORY_SEPARATOR], ['', '\\'], APP_PATH) . $directory . '\\',
            DIR_PATH . 'core' . DIRECTORY_SEPARATOR => 'core\\' . $directory . '\\IZY_'
        ];

        // Look for the class first in the local application/libraries folder
        // then in the native system/libraries folder
        foreach($namespaces as $path => $namespace)
        {
            if(file_exists($path . $directory . DIRECTORY_SEPARATOR . $class . '.php'))
            {
                if(class_exists($namespace . $class))
                {
                    $name = $namespace . $class;
                    break;
                }
            }
        }
        
        // Is the request a class extension? If so we load it too
        if($name !== FALSE)
        {
            if(file_exists(APP_PATH . $directory . DIRECTORY_SEPARATOR . 'MY_' . $class . '.php') && class_exists($namespaces[APP_PATH] . 'MY_' . $class))
            {
                $name = $namespaces[APP_PATH] . 'MY_' . $class;
            }
        }
        // Load the specified class
        else if(file_exists(APP_PATH . $directory . DIRECTORY_SEPARATOR . $class . '.php') && class_exists($namespaces[APP_PATH] . $class))
        {
            $name = $namespaces[APP_PATH] . $class;
        }
        
        try {
            // Did we find the class?
            if ($name === FALSE)
            {
                throw new \core\system\IZY_Exception('Fichier ' . APP_PATH . $directory . DIRECTORY_SEPARATOR . $class . '.php introuvable.');
                die;
            }

            // Keep track of class loaded
            system_loaded($directory, $class);
        }
        catch (\core\system\IZY_Exception $e)
        {
          echo $e;
        }

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

        $namespace = str_replace([DIR_PATH, DIRECTORY_SEPARATOR], ['', '\\'], MODELS_PATH);
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
        
        try {
            // Did we find the model?
            if ($name === FALSE)
            {
                throw new \core\system\IZY_Exception('Modèle ' . MODELS_PATH . $model . '.php introuvable.');
                die;
            }

            // Keep track of model loaded
            models_loaded($model);
        }
        catch (\core\system\IZY_Exception $e)
        {
          echo $e;
        }

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
     * @param    string
     * @return    array
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
     * @param    string
     * @return    array
     */
    function &system_loaded($directory, $class = '')
    {
        static $_is_loaded = [
            'helpers' => [],
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
        // Get instance
        $IZY =& get_instance();
        
        // 404 header
        $IZY->http->response_code('404');

        $routes = $IZY->router->routes;

        if($routes['404_url'] != '')
        {
            $IZY->router->set_path($routes['404_url']);

            $controller_404 = $IZY->router->controller;

            if(!empty($controller_404))
            {
                // Empty output content
                $IZY->output->empty();

                // Call controller
                $class = new $controller_404();
                call_user_func_array(array($class, $IZY->router->method), $IZY->router->args);

                // Unset controller
                unset($class);

                // Output
                $IZY->output->_display();
            }
        }

        die;
    }
}

if( ! function_exists('view'))
{
    function view($path, $datas = [])
    {
        // Get instance
        $IZY =& get_instance();
        
        // Convert datas array keys to vars
        if (!empty($datas))
        {
            extract($datas);
        }
        
        // Launch cache
        ob_start();

        // Pre_view hook
        $IZY->hooks->set_hook('pre_view');
        
        try {
            // View exist ?
            if (!is_file(VIEWS_PATH . (str_replace('/', DIRECTORY_SEPARATOR, $path)) . '.php'))
            {
                throw new \core\system\IZY_Exception('Vue ' . VIEWS_PATH . $path . '.php introuvable.', 1);
                die;
            }

            // Include view
            include(VIEWS_PATH . $path . '.php');
        }
        catch (\core\system\IZY_Exception $e)
        {
          echo $e;
        }

        // Post_view hook
        $IZY->hooks->set_hook('post_view');

        // Content view
        $content = ob_get_contents();

        // Fermeture du cache
        ob_end_clean();

        return $content;
    }
}
