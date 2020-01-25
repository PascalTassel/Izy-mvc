<?php

namespace app\helpers;

if(!defined("IZI")) die("DIRECT ACCESS FORBIDDEN");

class Url_helper extends \core\helpers\Url_helper
{
  public static function css_url($path = "")
  {
    return self::site_url("assets/css/" . $path);
  }

  public static function img_url($path = "")
  {
    return self::site_url("assets/im/" . $path);
  }

  public static function js_url($path = "")
  {
    return self::site_url("assets/js/" . $path);
  }
}
