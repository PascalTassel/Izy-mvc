<?php
namespace core\components;

if(!defined("IZI")) die("DIRECT ACCESS FORBIDDEN");

class Breadcrumb extends \core\libraries\IZI_Components
{

  private static $_segments = [];   // Breadcrumb segments

  public function __construct($name, $item)
  {
    parent::__construct();

    self::add_item(self::get_config("home"), \core\helpers\Url_helper::site_url());

    self::add_item($name, $item);
  }


  /**
  * Défini les liens self, prev et next de la page dans $canonicals
  */
  public static function add_item($name, $item)
  {
    if(!empty($name) && !empty($item))
    {
      $segment = array(
        "position" => count(self::$_segments),
        "name" => $name,
        "item" => $item
      );
      array_push(self::$_segments, $segment);
    }
  }


  /**
  * Défini les liens self, prev et next de la page dans $canonicals
  */
  public static function get_items()
  {
    return self::$_segments;
  }
}
