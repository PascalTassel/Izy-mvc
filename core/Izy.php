<?php

namespace core;

if(!defined('IZY')) die('DIRECT ACCESS FORBIDDEN');

// Global functions
require_once(DIR_PATH . 'core/Functions.php');

/**
* IZY Object
* @author https://www.izi-mvc.com
*/
class IZY
{
    private static $_instance = null;

    private function __construct()
    {
        // Hooks
        $this->hooks =& load_class('Hooks');

        // Pre-system hook
        $this->hooks->set_hook('pre_system');

        // Requests system classes as IZY attributes
        $this->url =& load_class('Url');
        $this->router =& load_class('Router', 'system', $this->url->request);

        // Responses system classes as attributes
        $this->http =& load_class('Http');
        $this->output =& load_class('Output');
        $this->load =& load_class('Load');
        $this->database =& load_class('Database');

        // Call Main controller (The future instance)
        $this->controller =& load_class('Controller');

        // Autoload classes
        $autoload = get_config('autoload');
        $autoload = (is_null($autoload) or (gettype($autoload) !== 'array')) ? [] : $autoload;
        
        // Helpers
        if(!isset($autoload['helpers']) or (gettype($autoload['helpers']) !== 'array'))
        {
            $autoload['helpers'] = [];
        }
        array_push($autoload['helpers'], 'Url_helper');
        
        foreach($autoload['helpers'] as $helper)
        {
            load_class($helper, 'helpers');
        }
        // Libraries
        if(isset($autoload['libraries']) and gettype($autoload['libraries']) === 'array')
        {
            foreach($autoload['libraries'] as $library => $args)
            {
                load_class($library, 'libraries', $args);
            }
        }
        // Models
        if(isset($autoload['models']) and gettype($autoload['models']) === 'array')
        {
            foreach($autoload['models'] as $model)
            {
                load_model($model);
            }
        }

        // Pre controller hook
        $this->hooks->set_hook('pre_controller');

        // Response code
        $this->http->response_code($this->router->response_code);

        // Response controller
        $response_controller = $this->router->controller;

        if(!empty($this->router->controller))
        {

            // Call controller
            $class = new $response_controller();

            get_instance()->{'controller'} = $class;

            call_user_func_array(array($class, $this->router->method), $this->router->args);

            // Post controller hook
            $this->hooks->set_hook('post_controller');

            // Unset controller
            unset($class);

            // Headers
            $this->http->send_headers();

            // Pre display hook
            $this->hooks->set_hook('pre_display');

            // Add canonical meta
            if($this->router->response_code != '404')
            {
                $this->output->canonical('canonical', $this->url->request);
            }

            // Output
            $this->output->_display();

            // Post display hook
            $this->hooks->set_hook('post_display');
        }
    }

    /**
    * Get single instance of IZY
    * and return it.
    * @return Izy
    */
    public static function &get_instance()
    {
        if(is_null(self::$_instance))
        {
            self::$_instance = new Izy();
        }

        return self::$_instance;
    }
}
