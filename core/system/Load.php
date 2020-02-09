<?php

namespace core\system;

if(!defined('IZY')) die('DIRECT ACCESS FORBIDDEN');

/**
* Load a hook, a controller or a view
* @author https://www.izi-mvc.com
*/
class IZY_Load
{

	/**
	* Load a helper
	* @param string $helpers, separated by ","
	* @return object Helper instance as IZY->*_helper
	*/
	public function helper($helpers = '')
	{
    $helpers = explode(',', str_replace(' ', '', $helpers));

		foreach($helpers as $helper)
		{
      $this->_load_class($helper, 'helpers');
		}
	}

	/**
	* Load a library
	* @param string $helpers, separated by ","
	* @return object Helper instance as IZY->*_helper
	*/
	public function library($libraries = '')
	{
    $libraries = explode(',', str_replace(' ', '', $libraries));

		foreach($libraries as $library)
		{
			$this->_load_class($library, 'libraries');
		}
	}

	/**
	* Load a model
	* @param string $models, separated by ","
	* @return object Model instance as IZY->*_model
	*/
	public function model($models = '')
	{
    $models = explode(',', str_replace(' ', '', $models));

		foreach($models as $model)
		{
			$class =& load_model($model);
			$var = str_replace('/', '_', strtolower($model));

			get_instance()->$var = $class;
		}
	}

	protected function _load_class($name, $dir)
	{
		$class =& load_class($name, $dir);
		$var = strtolower($name);

		get_instance()->$var = $class;
	}
}
