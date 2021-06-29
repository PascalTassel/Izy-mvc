<?php

// IZY
define('IZY', TRUE);

// DIRS
define('DIR_PATH', __DIR__ . DIRECTORY_SEPARATOR);
define('APP_PATH', DIR_PATH . 'app' . DIRECTORY_SEPARATOR);
define('CONFIG_PATH', APP_PATH . 'config' . DIRECTORY_SEPARATOR);
define('CONTROLLERS_PATH', APP_PATH . 'controllers' . DIRECTORY_SEPARATOR);
define('MODELS_PATH', APP_PATH . 'models' . DIRECTORY_SEPARATOR);
define('VIEWS_PATH', APP_PATH . 'views' . DIRECTORY_SEPARATOR);

// IZY CLASSES AUTOLOADING
spl_autoload_register(function ($class_name)
{
    $namespace = explode('\\', $class_name);
    $class = str_replace('IZY_', '', array_pop($namespace));
    $file = implode('/', $namespace) . '/' . $class . '.php';
    if (is_file($file))
    {
        include_once $file;
    }
});

// IZY INSTANCE
$IZY =& core\Izy::get_instance();

unset($IZY);
