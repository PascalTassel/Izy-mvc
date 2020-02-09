<?php

namespace core\system;

if(!defined('IZY')) die('DIRECT ACCESS FORBIDDEN');

/**
* Define path, namespace, controller, method and args
* @author https://www.izi-mvc.com
*/
class IZY_Database
{
  private $_config;             // Settings (set in app/config/config.php)
  private static $_is_loaded;   // Keep a trace of loaded databases

  public function __construct()
  {
    // Settings
    $settings = get_config('databases');
    if(!is_null($settings))
    {
      $this->_config = $settings;

      // Autoload db connections
  	  $autoload = get_config('autoload');
      foreach($autoload['databases'] as $db)
      {
        $var = strtolower($db);
        $this->$var = $this->connect($db);
      }
    }
  }

  public function connect($database)
	{
		if(isset(self::$_is_loaded[$database]))
		{
			return self::$_is_loaded[$database];
		}

    $config = get_config('databases');

		if(!isset($config[$database]))
		{
			echo 'Unable to locate the specified database ' . $database . ' config';
			exit(5); // EXIT_UNK_CLASS
		}

    // Database settings
    $db = $this->_config[$database];

    if(is_null($db))
    {
			echo 'Unknown ' . $db . 'database';
      die;
    }

    $dsn = 'mysql:host=' . $db['host']. ';dbname=' . $db['name']. ';charset=' . $db['charset'];

    // PDO Options
    $opts = [
        \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ
    ];

    try
    {
      self::$_is_loaded[$database] = new \PDO($dsn, $db['user'], $db['pwd'], $opts);

      return self::$_is_loaded[$database];
    }
    catch (\PDOException $e)
    {
      die('Unable to connect to the ' . $name . ' database : ' . $e->getMessage());
    }
	}
}
