<?php

namespace core\system;

if(!defined('IZY')) die('DIRECT ACCESS FORBIDDEN');

/**
* Set HTTP headers
* @author https://www.izi-mvc.com
*/
class IZY_Http
{
  private $_headers = [];
  private $_codes = array(                        // HTTP codes used for headers
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
  public function add_header($header)
  {
    array_push($this->_headers, $header);
  }

  /**
  * Set header location
  * @param string $url Url location
  */
	public function location($url = '')
	{
    if($url == '')
    {
      echo 'Location $url is empty.';
      die();
    }

    header('Location: ' . $url);
		die;
  }

  /**
  * Redirection
  * @param string $url Url to redirect
  * @param int $code header code
  */
	public function redirect($url, $replace = TRUE, $code = 302)
	{
    if($url == '')
    {
      echo 'Redirect $url is empty.';
      die();
    }

  	header('Location: ' . $url, $replace, $code);
		die;
	}

  /**
  * Send headers
  */
  public function send_headers()
  {
    foreach($this->_headers as $header)
    {
      header($header);
    }
  }

  /**
  * Set header code
  * @param int $code header code
  */
  public function response_code($code = '404')
  {
    if(!isset($this->_codes[$code]))
    {
      echo 'Code HTTP/1.1 <strong>' . $code . '</strong> not exists.';
      die();
    }

    header('HTTP/1.1 ' . $code . ' ' . $this->_codes[$code]);
  }
}
