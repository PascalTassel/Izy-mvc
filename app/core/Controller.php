<?php

namespace app\core;

if(!defined('IZI')) die('DIRECT ACCESS FORBIDDEN');

/**
* Abstract controller
* @author https://www.izi-mvc.com
*/
class Controller extends \core\libraries\IZI_Controller
{

  public function __construct()
  {
    parent::__construct();
  }

  /**
  * Default 404 method
  */
  public function show_404()
  {
    // 404 header
    \core\libraries\IZI_Http::set_code();

    // Layout
    \core\libraries\IZI_Output::set_layout([
      "path" => "layout/default",
      "title" => "Light Web Framework",
      "description" => "toto"
    ]);

    // View
    \core\libraries\IZI_Output::set_view('error_404');
  }
}
