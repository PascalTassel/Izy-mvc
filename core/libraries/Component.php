<?php

namespace core\libraries;

if(!defined('IZI')) die('DIRECT ACCESS FORBIDDEN');

/**
* Abstract component
* @author https://www.izi-mvc.com
*/
class IZI_Component
{
  private $config = [];
  private $component;

  public function __construct()
  {
    // Called component
    $this->component = get_called_class();

    // Remove namespace
		$output = explode('\\', $this->component);
    $this->component = $output[count($output) - 1];

    // Default settings
    if(isset(CONFIG['components'][$this->component]))
    {
      $this->config = CONFIG['components'][$this->component];
    }
  }

  /**
  * Get component settings
  * @param string $key Setting
  * @return Setting value
  */
  public function get_config($key = '')
  {
    try
    {
      if(gettype($key) != 'string')
      {
        throw new \core\libraries\IZI_Exception(ucfirst($this->component) . ' component. ' . $key . ' is not a string.');
      }
    }
    catch (IZI_Exception $e)
    {
      die($e);
    }

    return $key == '' ? $this->config : $this->config[$key];
  }

  /**
  * Set component setting
  * @param string $key Setting
  * @param string $value Setting value
  */
  public function set_config($key, $value)
  {
    $this->config[$key] = $value;
  }
}
