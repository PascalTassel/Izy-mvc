<?php
/**
* This file is part of the Izy_mvc project.
*
* Define required constants and autoload framework classes 
*
* @package Izy-mvc
* @copyright 2021 © Pascal Tassel for https://www.izy-mvc.com <contact[@]izy-mvc.com>
*/

// IZY
define('IZY', TRUE);

// Application directories
define('DIR_PATH', __DIR__ . DIRECTORY_SEPARATOR);
define('APP_PATH', DIR_PATH . 'app' . DIRECTORY_SEPARATOR);
define('CONFIG_PATH', APP_PATH . 'config' . DIRECTORY_SEPARATOR);
define('CONTROLLERS_PATH', APP_PATH . 'controllers' . DIRECTORY_SEPARATOR);
define('MODELS_PATH', APP_PATH . 'models' . DIRECTORY_SEPARATOR);
define('VIEWS_PATH', APP_PATH . 'views' . DIRECTORY_SEPARATOR);

// Autoload Izy classes
spl_autoload_register(function ($class_name)
{
    $namespace = explode('\\', $class_name);
    $class = str_replace('IZY_', '', array_pop($namespace));
    $file = implode('/', $namespace) . DIRECTORY_SEPARATOR . $class . '.php';
    if (is_file($file))
    {
        include_once $file;
    }
});

// Set IZY instance
try {
    $IZY =& core\Izy::get_instance();
}
catch (core\system\IZY_Exception $e)
{
    echo $e;
}

unset($IZY);
