<?php

namespace core\libraries;

if(!defined('IZI')) die('DIRECT ACCESS FORBIDDEN');

/**
* Segment uri
* @author https://www.izi-mvc.com
*/
class IZI_Url
{
  private static $_protocol;               // Protocol
  private static $_host;                   // Host
  private static $_url = '';               // URL
  private static $_queries = [];           // $_GET as array

  public static function get_host()
	{
		return self::$_host;
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

  public static function set_queries($datas = [])
	{
    try
    {
      if(gettype($datas) != "array")
      {
        throw new IZI_Exception('$datas queries must be an array.');
      }
    }
    catch (IZI_Exception $e)
    {
      die($e);
    }

    self::$_queries = $datas;
  }

  public static function set_uri($uri = '')
  {
    if($uri == '')
    {
      // Protocol
      self::$_protocol = $_SERVER['REQUEST_SCHEME'] != '' ? $_SERVER['REQUEST_SCHEME'] : 'http';
      if(isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on'))
      {
        self::$_protocol = 'https';
      }

      // Host
      try
      {
        if(!isset(CONFIG['host']) || empty(CONFIG['host']))
        {
          throw new IZI_Exception('Host not defined in \$config.');
        }

        self::$_host = CONFIG['host'];
      }
      catch (IZI_Exception $e)
      {
        die($e);
      }

      $uri = ltrim(str_replace(self::$_host, '', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']), '/');
    }
    else
    {
      self::$_protocol = parse_url($uri, PHP_URL_SCHEME);
      self::$_host = parse_url($uri, PHP_URL_USER);
    }

    // Url
    self::$_url = parse_url($uri, PHP_URL_PATH);

    // Queries
    $query_string = parse_url($uri, PHP_URL_QUERY);
    parse_str($query_string, self::$_queries);
  }
}
