<?php

namespace core\libraries;

if(!defined("IZI")) die("DIRECT ACCESS FORBIDDEN");

/**
* Parse input url and send it to IZI_Controller
* @author https://www.izi-mvc.com
*/
class IZI_Url
{
  private static $_instance = null;        // Url object
  private static $_router;                 // Router object

  private static $_protocol;               // Protocol
  private static $_host;                   // Host
  private static $_url = "";               // URL
  private static $_queries = [];           // $_GET as array

  public function __construct()
  {
    // Protocol
    self::$_protocol = $_SERVER["REQUEST_SCHEME"] != "" ? $_SERVER["REQUEST_SCHEME"] : "http";
    if(isset($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] === "on"))
    {
      self::$_protocol = "https";
    }

    // Host
    try
    {
      if(!isset(CONFIG["host"]) || empty(CONFIG["host"]))
      {
        throw new IZI_Exception("Host not defined in \$config.");
      }

      self::$_host = CONFIG["host"];
    }
    catch (IZI_Exception $e)
    {
      die($e);
    }

    // Uri
    $uri = ltrim(str_replace(self::$_host, "", $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]), "/");

    // Url
    self::$_url = parse_url($uri, PHP_URL_PATH);

    // Queries
    $query_string = parse_url($uri, PHP_URL_QUERY);
    parse_str($query_string, self::$_queries);

    // Call controller
    IZI_Load::controller(self::$_url, true);
  }

  public static function get_host()
  {
    return self::$_host;
  }

  public static function get_instance()
  {
    if(is_null(self::$_instance))
    {
      self::$_instance = new IZI_Url();
    }

    return self::$_instance;
  }

  public static function get_protocol()
  {
    return self::$_protocol;
  }

  public static function get_queries()
  {
    return self::$_queries;
  }

  public static function get_url()
  {
    return self::$_url;
  }

  public static function set_queries($queries)
  {
    try
    {
      if(gettype($queries) != "array")
      {
        throw new IZI_Exception("Argument of IZI_Url::set_queries() function must be an array.");
      }

      self::$_queries = $queries;
    }
    catch (IZI_Exception $e)
    {
      die($e);
    }
  }
}
