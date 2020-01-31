<?php

namespace app\controllers;

if(!defined("IZI")) die("DIRECT ACCESS FORBIDDEN");

class Welcome extends \core\libraries\IZI_Controller
{

  public function __construct()
  {
    parent::__construct();
  }

  /**
  * Display welcome view
  */
  public static function index()
  {
    // View
    \core\libraries\IZI_Output::set_view("welcome");
  }
}
