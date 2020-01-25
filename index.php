<?php

// IZI
define("IZI", true);

// DIRS
define("DIR_PATH", __DIR__ . "/");
define("APP_PATH", DIR_PATH . "app/");
define("CONFIG_PATH", APP_PATH . "config/");
define("CONTROLLERS_PATH", APP_PATH . "controllers/");
define("LAYOUT_PATH", APP_PATH . "layout/");
define("VIEWS_PATH", APP_PATH . "views/");

// AUTOLOADING
spl_autoload_register(function ($class_name)
{
  $namespace = explode("\\", $class_name);
  $file = str_replace("IZI_", "", implode("/", $namespace)) . ".php";

  if(is_file($file))
	{
    include_once $file;
  }
});

// GET CONFIG
try
{
  if(!is_file(CONFIG_PATH . "config.php"))
  {
    throw new core\libraries\IZI_Exception("File " . CONFIG_PATH . "config.php not found.");
  }
  include(CONFIG_PATH . "config.php");

  // Custom config
  foreach(scandir(CONFIG_PATH) as $file)
  {
    if(preg_match("#_config.php$#", $file))
    {
      include(CONFIG_PATH . $file);
    }
  }
  define("CONFIG", $config);
}
catch (core\libraries\IZI_Exception $e)
{
  die($e);
}

// PRE SYSTEM HOOK
core\libraries\IZI_Load::hook("pre_system");

// URL INSTANCE
$url = core\libraries\IZI_Url::get_instance();

// OUTPUT VIEW
core\libraries\IZI_Output::display();

unset($url);
