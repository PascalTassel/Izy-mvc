<?php

namespace app\controllers;

if(!defined('IZY')) die('DIRECT ACCESS FORBIDDEN');

class Welcome extends \core\system\IZY_Controller
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
    $this->output->view('welcome');
  }
}
