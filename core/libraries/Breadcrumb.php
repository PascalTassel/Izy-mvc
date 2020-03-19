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
  public $first = [];         // Default first segment

  public function __construct(array $datas = [])
  {
    // Get config settings
    parent::__construct('Breadcrumb');

    // Add first
    if(count($this->first) == 2)
    {
      $this->add_segment($this->first[0], $this->first[1]);
    }
  }

  /**
  * Add a segment in breadcrumb
  * @param string $label Segment label
  * @param string $url Segment url
  */
  public function first(string $label, string $url)
  {
    array_shift($this->segments);
    reset($this->segments);

    $this->add_segment($label, $url);
  }

  /**
  * Add a segment in breadcrumb
  * @param string $label Segment label
  * @param string $url Segment url
  */
  public function add_segment(string $label, string $url = '')
  {
    $this->segments[$label] = $url;
  }

  /**
  * Add a segment in breadcrumb
  * @param string $label Segment label
  * @param string $url Segment url
  */
  public function get_segments()
  {
    return $this->segments;
  }
}
