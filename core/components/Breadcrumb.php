<?php
namespace core\components;

if(!defined('IZI')) die('DIRECT ACCESS FORBIDDEN');

/**
* Breadcrumb component
* @author https://www.izi-mvc.com
*/
class Breadcrumb extends \core\libraries\IZI_Component
{

  private static $_segments = [];   // Breadcrumb as array

  public function __construct()
  {
    parent::__construct();
  }

  public static function get_segments()
  {
    return self::$_segments;
  }

  /**
  * Add segment in breadcrumb
  * @param string $label Segment label
  * @param string $link Segment link
  */
  public static function add_item($label, $link = '')
  {
    try
    {
      if($label == '')
      {
        throw new IZI_Exception('Breadcrumb component. Label argument is empty.');
      }

      self::$_segments[$label] = $link;
    }
    catch (IZI_Exception $e)
    {
      die($e);
    }
  }
}
