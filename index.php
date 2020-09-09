<?php

// IZY
define('IZY', TRUE);

// DIRS
define('DIR_PATH', __DIR__ . DIRECTORY_SEPARATOR);
define('APP_PATH', DIR_PATH . 'app/');
define('CONFIG_PATH', APP_PATH . 'config/');
define('CONTROLLERS_PATH', APP_PATH . 'controllers/');
define('MODELS_PATH', APP_PATH . 'models/');
define('VIEWS_PATH', APP_PATH . 'views/');

// AUTOLOADING
spl_autoload_register(function ($class_name)
{
    $namespace = explode('\\', $class_name);
    $class = str_replace('IZY_', '', array_pop($namespace));
    $file = implode('/', $namespace) . '/' . $class . '.php';
    if(is_file($file))
    {
        include_once $file;
    }
});

$IZY =& core\Izy::get_instance();

unset($IZY);
