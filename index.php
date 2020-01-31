<?php

// IZI
define('IZI', TRUE);

// DIRS
define('DIR_PATH', __DIR__ . DIRECTORY_SEPARATOR);
define('APP_PATH', DIR_PATH . 'app/');
define('CONFIG_PATH', APP_PATH . 'config/');
define('CONTROLLERS_PATH', APP_PATH . 'controllers/');
define('VIEWS_PATH', APP_PATH . 'views/');

// AUTOLOADING
spl_autoload_register(function ($class_name)
{
  $namespace = explode('\\', $class_name);
  $file = str_replace('IZI_', '', implode('/', $namespace)) . '.php';

  if(is_file($file))
	{
    include_once $file;
  }
});

// GET CONFIG
try
{
  if(!is_file(CONFIG_PATH . 'config.php'))
  {
    throw new core\libraries\IZI_Exception('File ' . CONFIG_PATH . 'config.php not found.');
  }
  include(CONFIG_PATH . 'config.php');

  // Custom config
  foreach(scandir(CONFIG_PATH) as $file)
  {
    if(preg_match('#_config.php$#', $file))
    {
      include(CONFIG_PATH . $file);
    }
  }
  define('CONFIG', $config);
}
catch (core\libraries\IZI_Exception $e)
{
  die($e);
}

// URI
core\libraries\IZI_Url::set_uri();

// PRE SYSTEM HOOK
core\libraries\IZI_Load::hook('pre_system');

// LOAD CONTROLLER
core\libraries\IZI_Load::controller();

// OUTPUT VIEW
core\libraries\IZI_Output::_display();
