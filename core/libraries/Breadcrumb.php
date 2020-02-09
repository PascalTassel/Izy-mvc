<?php
namespace core\libraries;

if(!defined('IZY')) die('DIRECT ACCESS FORBIDDEN');

/**
* Pagination component
* @author https://www.izi-mvc.com
*/
class IZY_Breadcrumb extends IZY_Library
{
  // Default settings
  public $segments = [];      // Breadcrumb segments
  public $first = ['Home'];   // Default first segment

  public function __construct(array $datas = [])
  {
    // Get config settings
    parent::__construct('Breadcrumb');

    // Default first segment url
    array_push($this->first, get_instance()->url_helper->site_url());

    // Settings
    foreach($datas as $setting => $value)
    {
      $this->$setting = $value;
    }

    // Add first
    $this->_add_segment($this->first);
  }

  /**
  * Add a segment in breadcrumb
  * @param string $label Segment label
  * @param string $link Segment link
  */
  protected function _add_segment(array $segment = [])
  {
    if(count($segment) >= 1)
    {
      $this->segments[$segment[0]] = isset($segment[1]) && ($segment[1] != '') ? (string) $segment[1] : NULL;
    }
  }
}
