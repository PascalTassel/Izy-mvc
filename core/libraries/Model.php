<?php

namespace core\libraries;

if(!defined('IZI')) die('DIRECT ACCESS FORBIDDEN');

/**
* Abstract model
* @author https://www.izi-mvc.com
*/
class IZI_Model
{
	/**
  * Database connection
  * @param string $name Db name (set in $config['db'])
	* $path is only empty when called by IZI_Url instance
  */
  protected function db_connect($name)
  {
    $config = CONFIG['db'];
    $db = $config[$name];
    // Paramètres de connexion
    $host = $db['host'];
    $database = $db['name'];
    $charset = $db['charset'];
    $user = $db['user'];
    $pwd = $db['pwd'];
    $dsn = 'mysql:host=' . $host. ';dbname=' . $database. ';charset=' . $charset;

    // PDO Options
    $opts = [
        \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ
    ];

    try
    {
      $pdo = new \PDO($dsn, $user, $pwd, $opts);
      return $pdo;
    }
    catch (\PDOException $e)
    {
      die('Unable to connect to the ' . $name . ' database : ' . $e->getMessage());
    }
  }
}
