<?php
namespace core;

if(!defined('IZY')) die('DIRECT ACCESS FORBIDDEN');

/**
* This file is part of the Izy_mvc project.
*
* Set an instance of main controller IZY_Controller
* 1 - All system classes, models and helpers are loaded from the constructor and defined as attributes
* 2 - IZY_Url class get request
* 3 - IZY_Router class build response according to the request (define class, method and arguments)
* 4 - Response controller (which extends main controller) is called and performs the output
* 5 - IZY_Http set HTTP header code according to the response (200 or 404)
* 5 - IZY_Http add optionnals HTTP headers (defined in response controller)
* 6 - IZY_Output write response or show 404 (if defined)
*
* @package Izy-mvc
* @copyright 2021 © Pascal Tassel for https://www.izy-mvc.com <contact[@]izy-mvc.com>
*/

// Load Global functions
require_once(DIR_PATH . 'core' . DIRECTORY_SEPARATOR . 'Functions.php');

// Get Autoloading files (defined in $config['autoloading'])
$autoloading = get_config('autoloading');
if (!is_null($autoloading)) 
{
    foreach ($autoloading as $autoload)
    {
        // File exist ?
        if (file_exists(DIR_PATH . $autoload) === false)
        {
            throw new system\IZY_Exception('$config[\'autoload\'] doit être un tableau associatif. Exemple&nbsp;: $config[\'autoload\'][\'helpers\']');
        }
        // Include autoload file
        require_once(DIR_PATH . $autoload);
    }
}

/**
* IZY class performs different tasks:
*
* 1 - Store an instance of each Izy system class in an attribute using load_class() global function.
* 2 - Load helpers and models defined in $config['autoload'] using load_class() global function.
*    (A trace of each loaded class with load_class() is stored in an array in system_loaded() global function)
* 3 - Send HTTP Headers (depending on the response of the router)
* 4 - Call response controller (depending on the response of the router) or 404 controller (if defined in $routes['404_url'])
* 5 - Add canonical meta tag in output (depending on the response of the router)
* 6 - Write response (depending on the response controller)
*
*   (Various hooks classes, defined in $config['hooks'] are called between this steps)
*
* @author Pascal Tassel <contact[@]izy-mvc.com>
*/
class IZY
{
    private static $_instance = null;

    private function __construct()
    {
        // Define Izy_Hooks as attribute
        $this->hooks =& load_class('Hooks');

        // Call Pre_system class
        $this->hooks->set_hook('pre_system');

        // Define Izy_Url as attribute
        $this->url =& load_class('Url');
        // Define Izy_Router  as attribute (passing the request as argument)
        $this->router =& load_class('Router', 'system', $this->url->request);

        // Define remaining Izy classes as attributes
        $this->http =& load_class('Http');
        $this->output =& load_class('Output');
        $this->load =& load_class('Load');
        $this->database =& load_class('Database');

        // Define main controller (The future instance) as attribute
        $this->controller =& load_class('Controller');

        // Get helpers and models to autoload
        $autoload = get_config('autoload');
        
        // Create $autoload array if not exist
        $autoload = is_null($autoload) ? [] : $autoload;
        
        // Is an array ?
        if (gettype($autoload) !== 'array')
        {
            throw new system\IZY_Exception('$config[\'autoload\'] doit être un tableau associatif. Exemple&nbsp;: $config[\'autoload\'][\'helpers\']');
        }
        
        // Add 'helpers' key to $autoload array if not exist
        if (!isset($autoload['helpers']))
        {
            $autoload['helpers'] = [];
        }
        // If not, check if is an array
        else if (gettype($autoload['helpers']) !== 'array')
        {
            throw new system\IZY_Exception('$autoload[\'helpers\'] doit être un tableau. Exemple&nbsp;: [\'App_helper\']');
        }
            
        // Add 'Izy Url_helper' to autoloaded helpers
        array_push($autoload['helpers'], 'Url_helper');
        
        // Load helpers
        foreach ($autoload['helpers'] as $helper)
        {
            load_class($helper, 'helpers');
        }
        
        // Load models
        if (isset($autoload['models']))
        {
            // Check if is an array
            if (gettype($autoload['models']) !== 'array')
            {
                throw new system\IZY_Exception('$autoload[\'models\'] doit être un tableau. Exemple&nbsp;: [\'Blog_model\']');
            }
            
            foreach ($autoload['models'] as $model)
            {
                load_model($model);
            }
        }

        // Call pre_controller class
        $this->hooks->set_hook('pre_controller');

        // Set Header HTTP response code (depending on the response of the router)
        $this->http->response_code($this->router->response_code);

        // Retrieve the controller called by the request
        $response_controller = $this->router->controller;
        
        // If controller found by the router
        if (!empty($this->router->controller))
        {
            // Get called controller instance
            $class = new $response_controller();
            
            // Assign called controller as attribute of instance (main controller)
            get_instance()->{'controller'} = $class;
            
            // Call controller method passing arguments (depending on the response of the router)
            call_user_func_array(array($class, $this->router->method), $this->router->args);

            // Call post_controller class
            $this->hooks->set_hook('post_controller');

            // Unset called controller
            unset($class);

            // Send HTTP Headers
            $this->http->send_headers();

            // Call pre_display class
            $this->hooks->set_hook('pre_display');

            // Add canonical meta tag in $canonicals array
            if($this->router->response_code != '404')
            {
                $this->output->canonical('canonical', $this->url->request);
            }

            // Write output
            $this->output->_display();

            // Call post_display class
            $this->hooks->set_hook('post_display');
        }
    }

    /**
    * Return the single IZY instance
    *
    * @return object IZY instance
    */
    public static function &get_instance()
    {
        if (is_null(self::$_instance))
        {
            self::$_instance = new Izy();
        }

        return self::$_instance;
    }
}
