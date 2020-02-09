<?php
namespace core\libraries;

if(!defined('IZY')) die('DIRECT ACCESS FORBIDDEN');

/**
* Pagination component
* @author https://www.izi-mvc.com
*/
class IZY_Library
{
  public function __construct($library = '')
  {
    // Library settings
    if($library !== '')
    {
      $settings = get_config('libraries');

      if(isset($settings[$library]))
      {
        foreach($settings[$library] as $var => $value)
        {
          $this->$var = $value;
        }
      }
    }
  }
}
