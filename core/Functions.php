<?php
if(!defined('IZY')) die('DIRECT ACCESS FORBIDDEN');

/**
* This file is part of the Izy_mvc project.
*
* Global functions
*
* @package Izy-mvc
* @copyright 2021 © Pascal Tassel for https://www.izy-mvc.com <contact[@]izy-mvc.com>
*/

/**
* Get a configuration parameter define in $config array
*
* @param string $item Name of the parameter
*
* @return mixed Expected configuration parameter or NULL
*/
function get_config($item = '')
{
    $config =& get_config_files();

    return isset($config[$item])
        ? $config[$item]
        : NULL;
}

/**
* Get configuration files defined in /app/config/
*
* @throws IZY_Exception
*
* @return array Configuration parameters
*/
function &get_config_files()
{
    static $_config;

    if (empty($_config))
    {
        $config_files = [];

        // Isset config file ?
        if (is_file(CONFIG_PATH . 'config.php'))
        {
            // Include config file
            include(CONFIG_PATH . 'config.php');
            
            array_push($config_files, 'config.php');
        }

        // Include custom config files
        try {

            foreach (scandir(CONFIG_PATH) as $file)
            {
                if (preg_match('#_config.php$#', $file))
                {
                    include(CONFIG_PATH . $file);
    
                    array_push($config_files, $file);
                }
            }

            // Isset config files ?
            if (count($config_files) === 0)
            {
                throw new \core\system\IZY_Exception('Aucun fichier de configuration défini dans ' . CONFIG_PATH);
                die;
            }

            try {
                // Isset $config ?
                if (!isset($config))
                {
                    throw new \core\system\IZY_Exception('Tableau $config non défini un fichier de configuration.');
                    die;
                }
                
                $_config = $config;
            }
            catch (\core\system\IZY_Exception $e)
            {
            echo $e;
            }
        }
        catch (\core\system\IZY_Exception $e)
        {
          echo $e;
        }
    }

    return $_config;
}

/**
* Get Main controller instance
*
* @return object Main controller (IZY_Controller)
*/
function &get_instance()
{
    $controller =& load_class('Controller');
    return $controller::get_instance();
}

/**
* Load a [system | helper] class
*
* @param string $class Name of the class to load
* @param string $class Directory class location
* @param string|null $param arguments class comma separated
*
* @throws IZY_Exception
*
* @return object Expected class
*/
function &load_class($class, $directory = 'system', $param = NULL)
{
    static $_classes = [];

    if (isset($_classes[$class]))
    {
        return $_classes[$class];
    }

    // Look for the class first in app folder
    // Then in core folder
    $name = FALSE;
    $namespaces = [
        APP_PATH => str_replace([DIR_PATH, DIRECTORY_SEPARATOR], ['', '\\'], APP_PATH) . $directory . '\\',
        DIR_PATH . 'core' . DIRECTORY_SEPARATOR => 'core\\' . $directory . '\\IZY_'
    ];

    foreach ($namespaces as $path => $namespace)
    {
        if (file_exists($path . $directory . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $class) . '.php'))
        {
            if (class_exists($namespace . $class))
            {
                $name = $namespace . $class;
                break;
            }
        }
    }
    
    // Is a class extension (starting by 'MY_')? If so we load it
    if ($name !== FALSE)
    {
        if (file_exists(APP_PATH . $directory . DIRECTORY_SEPARATOR . 'MY_' . str_replace('/', DIRECTORY_SEPARATOR, $class) . '.php') && class_exists($namespaces[APP_PATH] . 'MY_' . $class))
        {
            $name = $namespaces[APP_PATH] . 'MY_' . $class;
        }
    }
    // Load the specified class
    else if (file_exists(APP_PATH . $directory . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $class) . '.php') && class_exists($namespaces[APP_PATH] . $class))
    {
        $name = $namespaces[APP_PATH] . $class;
    }
    
    try {
        // We don't find the class?
        if ($name === FALSE)
        {
            throw new \core\system\IZY_Exception('Fichier ' . APP_PATH . $directory . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $class) . '.php introuvable.');
            die;
        }

        // Keep trace of loaded class
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

/**
* Load a model class
*
* @param string $model Name of the class to load
*
* @throws IZY_Exception
*
* @return object Expected model
*/
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
    if (count($segments) > 1)
    {
        $class = end($segments);
        $pop = array_pop($segments);
        $namespace .= implode('\\', $segments) . '\\';
    }

    $class = $namespace . $class;

    // Look for the model in app/model/ dir
    if (file_exists(MODELS_PATH . str_replace('/', DIRECTORY_SEPARATOR, $model) . '.php') && class_exists($class))
    {
        $name = $class;
    }
    
    try {
        // Did we find the model?
        if ($name === FALSE)
        {
            throw new \core\system\IZY_Exception('Modèle ' . MODELS_PATH . str_replace('/', DIRECTORY_SEPARATOR, $modelhelper) . '.php introuvable.');
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

/**
* Keeps trace of which models have been loaded.
* This function is called by the load_model() function above
*
* @param string $model Name of the model to keep trace
*
* @return object Expected loaded model
*/
function &models_loaded($model = '')
{
    static $_is_loaded = [];

    if ($model != '')
    {
        $_is_loaded[strtolower($model)] = $model;
    }

    return $_is_loaded;
}

/**
* Keeps trace of which classes have been loaded.
* This function is called by the load_class() function above
*
* @param string $directory Directory class location
* @param string $class Name of the loaded class
*
* @return array Loaded classes into the expected directory
*/
function &system_loaded($directory, $class = '')
{
    static $_is_loaded = [
        'helpers' => [],
        'system' => []
    ];

    if ($class != '')
    {
        $_is_loaded[$directory][strtolower($class)] = $class;
    }

    return $_is_loaded[$directory];
}

/**
* Send HTTP 404 and display 404 view (if defined in $routes['404_url'])
*
* @return void 404 error
*/
function show_404()
{
    // Get instance
    $IZY =& get_instance();
    
    // 404 header
    $IZY->http->response_code('404');

    $routes = $IZY->router->get_routes();

    if ($routes['404_url'] != '')
    {
        $IZY->router->set_response($routes['404_url']);

        $controller_404 = $IZY->router->get_controller();

        if (!empty($controller_404))
        {
            // Empty output content
            $IZY->output->empty();

            // Call 404 controller
            $class = new $controller_404();
            call_user_func_array(array($class, $IZY->router->get_method()), $IZY->router->get_args());

            // Unset 404 controller
            unset($class);

            // Output
            $IZY->output->_display();
        }
    }
    die;
}

/**
* Get content view
* pre_view hook class is called before view include
* post_view hook class is called after view include
*
* @param string $path Path to expected view
* @param array $datas passed into view
*
* @throws IZY_Exception
*
* @return string Output view
*/
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

    // Content view
    $content = ob_get_contents();

    // Close cache
    ob_end_clean();

    return $content;
}
