<?php

namespace core\libraries;

if(!defined('IZI')) die('DIRECT ACCESS FORBIDDEN');

/**
* Abstract controller
* @author https://www.izi-mvc.com
*/
class IZI_Controller
{

  public function __construct()
  {

  }

  /**
  * Load a model in controller
  * @param string $model Model namespace
  * @return object Model
  */
  protected function get_model($model)
  {
    try
    {
      if(!class_exists($model))
    	{
        throw new \core\libraries\IZI_Exception('Model ' . $model . ' not found.');
      }

      return new $model();
    }
    catch(\core\libraries\IZI_Exception $e)
    {
      die($e);
    }
  }

  /**
  * Default 404 method
  */
  public function show_404()
  {
    // 404 header
    IZI_Http::set_code();

    // View
    \core\libraries\IZI_Output::set_view('error_404');
  }
}
