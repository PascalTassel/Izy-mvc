<?php

namespace core\libraries;

if(!defined('IZI')) die('DIRECT ACCESS FORBIDDEN');

/**
* Set HTTP headers
* @author https://www.izi-mvc.com
*/
class IZI_Http
{

  public static $headers = [];

  // HTTP codes used for headers
  public static $codes = array(
                  '200' => 'OK',
                  '201' => 'Created',
                  '202' => 'Accepted',
                  '203' => 'Non-Authoritative Information',
                  '204' => 'No Content',
                  '205' => 'Reset Content',
                  '206' => 'Partial Content',

                  '300' => 'Multiple Choices',
                  '301' => 'Moved Permanently',
                  '302' => 'Found',
                  '304' => 'Not Modified',
                  '305' => 'Use Proxy',
                  '307' => 'Temporary Redirect',

                  '400' => 'Bad Request',
                  '401' => 'Unauthorized',
                  '403' => 'Forbidden',
                  '404' => 'Not Found',
                  '405' => 'Method Not Allowed',
                  '406' => 'Not Acceptable',
                  '407' => 'Proxy Authentication Required',
                  '408' => 'Request Timeout',
                  '409' => 'Conflict',
                  '410' => 'Gone',
                  '411' => 'Length Required',
                  '412' => 'Precondition Failed',
                  '413' => 'Request Entity Too Large',
                  '414' => 'Request-URI Too Long',
                  '415' => 'Unsupported Media Type',
                  '416' => 'Requested Range Not Satisfiable',
                  '417' => 'Expectation Failed',

                  '500' => 'Internal Server Error',
                  '501' => 'Not Implemented',
                  '502' => 'Bad Gateway',
                  '503' => 'Service Unavailable',
                  '504' => 'Gateway Timeout',
                  '505' => 'HTTP Version Not Supported'
  );

  /**
  * Add header
  * @param string $header Header content
  * @param boolean $replace Replace prev header
  */
  public static function add_header($header)
  {
    array_push(self::$headers, $header);
  }

  /**
  * Set header location
  * @param string $url Url location
  */
	public static function location($url)
	{
    try
    {
      if($url == '')
      {
        throw new Exception('Location $url is empty.');
      }

      // Empty output
  		IZI_Output::_flush();

      header('Location: ' . $url);
    }
    catch (IZI_Exception $e)
    {
      die($e);
    }
  }

  /**
  * Redirection
  * @param string $url Url to redirect
  * @param int $code header code
  */
	public static function redirect($url, $replace = TRUE, $code = 302)
	{
    try
    {
      if($url == '')
      {
        throw new Exception('Redirect $url is empty.');
      }

      // Empty output
  		IZI_Output::_flush();

  		header('Location: ' . $url, $replace, $code);
    }
    catch (IZI_Exception $e)
    {
      die($e);
    }
		die;
	}

  /**
  * Send headers
  */
  public static function send_headers()
  {
    foreach(self::$headers as $header)
    {
      header($header);
    }
  }

  /**
  * Set header code
  * @param int $code header code
  */
  public static function set_code($code = 404)
  {
    try
    {
      if(!isset(self::$codes[$code]))
      {
        throw new Exception('Code HTTP/1.1 <strong>' . $code . '</strong> not exists.');
      }

      header('HTTP/1.1 ' . $code . ' ' . self::$codes[$code]);
    }
    catch (IZI_Exception $e)
    {
      die($e);
    }
  }
}
