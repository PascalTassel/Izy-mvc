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
                    throw new IZY_Exception('$config[\'databases\'] doit être un tableau associatif.');
                    die;
                }
                self::$_config = $settings;
            }
            catch (IZY_Exception $e)
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
                        throw new IZY_Exception('$config[\'databases\'] doit être un tableau associatif.');
                        die;
                    }
                    
                    foreach($autoload['databases'] as $db)
                    {
                        $var = strtolower($db);
                        $this->$var = $this->connect($db);
                    }
                }
                catch (IZY_Exception $e)
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
            
            self::$_is_loaded[$database] = new \PDO($dsn, $db['user'], $db['pwd'], $opts);
        }
        catch (\PDOException $pe)
        {
            try {
                throw new IZY_Exception('Connection impossible à la base ' . $database .' : ' . $pe->getMessage());
                die;
            }
            catch (IZY_Exception $e)
            {
              echo $e;
            }
        }

        return self::$_is_loaded[$database];
    }
}
