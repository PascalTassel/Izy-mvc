<?php
namespace core\system;

if(!defined('IZY')) die('DIRECT ACCESS FORBIDDEN');

/**
* Load a helper, a library or a model
* @author https://www.izy-mvc.com
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
            $this->_load_class(str_replace('/', DIRECTORY_SEPARATOR, $helper), 'helpers');
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
            $class =& load_model(str_replace('/', DIRECTORY_SEPARATOR, $model));
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
