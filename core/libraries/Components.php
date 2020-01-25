<?php

namespace core\libraries;

if(!defined("IZI")) die("DIRECT ACCESS FORBIDDEN");

class IZI_Components
{
  private $config = [];

  public function __construct()
  {
    // Called component
    $component = get_called_class();

    // Remove namespace
		$output = explode("\\", $component);
    $component = $output[count($output) - 1];

    // Default settings
    $this->config = CONFIG["components"][$component];
  }

  /**
  * Get component settings
  * @param string $key Setting
  * @return Setting value
  */
  public function get_config($key = "")
  {
    return $key == "" ? $this->config : $this->config[$key];
  }
}
