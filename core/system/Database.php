<?php
namespace core\system;

if(!defined('IZY')) die('DIRECT ACCESS FORBIDDEN');

/**
* Database utilities
* @author https://www.izy-mvc.com
*/
class IZY_Database
{
    private static $_config;      // Settings (set in app/config/config.php)
    private static $_is_loaded;   // Keep a trace of loaded databases

    public function __construct()
    {
        // Settings
        $settings = get_config('databases');
        if(!is_null($settings))
        {
            
            try {
                if (gettype($settings) != 'array')
                {
                    throw new \core\system\IZY_Exception('$config[\'databases\'] doit être un tableau associatif.');
                    die;
                }
                self::$_config = $settings;
            }
            catch (\core\system\IZY_Exception $e)
            {
              echo $e;
            }

            // Autoload db connections
            $autoload = get_config('autoload');
            if (!is_null($autoload) and isset($autoload['databases'])) 
            {
                try {
                    if (gettype($autoload['databases']) !== 'array')
                    {
                        throw new \core\system\IZY_Exception('$config[\'databases\'] doit être un tableau associatif.');
                        die;
                    }
                    
                    foreach($autoload['databases'] as $db)
                    {
                        $var = strtolower($db);
                        $this->$var = $this->connect($db);
                    }
                }
                catch (\core\system\IZY_Exception $e)
                {
                  echo $e;
                }
            }
        }
    }

    public function connect($database)
    {
        if(isset(self::$_is_loaded[$database]))
        {
            return self::$_is_loaded[$database];
        }

        // Database settings
        $db = self::$_config[$database];
        
        try
        {
            $dsn = 'mysql:host=' . $db['host']. ';dbname=' . $db['name']. ';charset=' . $db['charset'];

            // PDO Options
            $opts = [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ
            ];
        }
        catch (\PDOException $e)
        {
            die('Unable to locate the specified database ' . $database . ' config : ' . $e->getMessage());
        }
        
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
