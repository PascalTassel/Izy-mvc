<?php

namespace core\system;

if(!defined('IZY')) die('DIRECT ACCESS FORBIDDEN');

/**
* Main controller
* @author https://www.izy-mvc.com
*/
class IZY_Controller
{
  private static $_instance;

  public function __construct($response_controller = '')
  {
		self::$_instance =& $this;

		// Assign [helpers, libraries, system] classses objects to local variables
		foreach(system_loaded('system') as $var => $system_class)
		{
      if($var != 'controller')
			{
        $this->$var =& load_class($system_class, 'system');
      }
		}
		foreach(system_loaded('helpers') as $var => $system_class)
		{
      $this->$var =& load_class($system_class, 'helpers');
		}
		foreach(system_loaded('libraries') as $var => $system_class)
		{
      $this->$var =& load_class($system_class, 'libraries');
		}

		// Assign models to local variables
		foreach(models_loaded() as $var => $model)
		{
      $this->$var =& load_model($model);
		}
  }

	/**
	 * Get the Controller instance
	 * @return object
	 */
	public static function &get_instance()
	{
    return self::$_instance;
	}
}
