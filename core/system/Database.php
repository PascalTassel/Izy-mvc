<?php
namespace core\system;

if(!defined('IZY')) die('DIRECT ACCESS FORBIDDEN');

/**
* Database utilities
*
* @package Izy-mvc
* @copyright 2021 © Pascal Tassel for https://www.izy-mvc.com <contact[@]izy-mvc.com>
*/
class IZY_Database
{
    private static $_config;      // Settings (set in app/config/config.php)
    
    /**
    * Get databases settings
    *
    * @throws IZY_Exception
    *
    * @return void Attribute definition
    */
    public function __construct()
    {
        // Settings
        $settings = get_config('databases');
        if (!is_null($settings))
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
                }
                catch (IZY_Exception $e)
                {
                  echo $e;
                }
                
                foreach ($autoload['databases'] as $db)
                {
                    $attribute = strtolower($db);
                    echo $attribute;
                    $this->$attribute = $this->connect($db);
                }
            }
        }
    }
    

    /**
    * Create database connection
    *
    * @param string $database Database name
    *
    * @throws IZY_Exception
    *
    * @return object PDO object
    */
    public function connect($database)
    {
        $attribute = strtolower($database);
        if (property_exists($this, $attribute))
        {
            return $this->$attribute;
        }

        // Database settings
        $db = self::$_config[$database];
        try {
            // Not isset host?
            if (!isset($db['host']) || (gettype($db['host']) !== 'string')) {
                throw new IZY_Exception('Paramètre host incorrect pour la base de donnée&nbsp;: ' . $database);
                die;
            }
            // Not isset name?
            else if (!isset($db['name']) || (gettype($db['name']) !== 'string'))
            {
                throw new IZY_Exception('Paramètre name incorrect pour la base de donnée&nbsp;: ' . $database);
                die;
            }
            // Not isset charset?
            else if (!isset($db['charset']) || (gettype($db['charset']) !== 'string'))
            {
                throw new IZY_Exception('Paramètre charset incorrect pour la base de donnée&nbsp;: ' . $database);
                die;
            }
            // Not isset user?
            else if (!isset($db['user']) || (gettype($db['user']) !== 'string'))
            {
                throw new IZY_Exception('Paramètre user incorrect pour la base de donnée&nbsp;: ' . $database);
                die;
            }
            // Not isset pwd?
            else if (!isset($db['pwd']) || (gettype($db['pwd']) !== 'string'))
            {
                throw new IZY_Exception('Paramètre pwd incorrect pour la base de donnée&nbsp;: ' . $database);
                die;
            }
            
            $dsn = 'mysql:host=' . $db['host'] . ';dbname=' . $db['name']. ';charset=' . $db['charset'];
        }
        catch (IZY_Exception $e)
        {
          echo $e;
        }
        
        try
        {
            // PDO Options
            $opts = [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ
            ];
            $this->$attribute = new \PDO($dsn, $db['user'], $db['pwd'], $opts);

            return $this->$attribute;
        }
        catch (\PDOException $e)
        {
            echo $e->getMessage();
        }
    }
}
