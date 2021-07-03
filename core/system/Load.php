<?php
namespace core\system;

if(!defined('IZY')) die('DIRECT ACCESS FORBIDDEN');

/**
* Load a helper, a library or a model
*
* @package Izy-mvc
* @copyright 2021 © Pascal Tassel for https://www.izy-mvc.com <contact[@]izy-mvc.com>
*/
class IZY_Load
{
    /**
    * Load helpers into a controller
    *
    * @param string $helpers Helpers paths comma separated
    *
    * @throws IZY_Exception
    *
    * @return void Adding helpers instances as controller attributes
    */
    public function helper($helpers = '')
    {
        try {
            // Not string ?
            if (gettype($helpers) !== 'string')
            {
                throw new IZY_Exception('L\'argument de la méthode $this->load->helper() doit être une chaîne de caractères.', 1);
                die;
            }
            
            $helpers = explode(',', str_replace(' ', '', $helpers));
        }
        catch (IZY_Exception $e)
        {
          echo $e;
        }

        foreach ($helpers as $helper)
        {
            $class =& load_class(str_replace('/', DIRECTORY_SEPARATOR, $helper), 'helpers');
            $var = strtolower($helper);

            get_instance()->$var = $class;
        }
    }
    
    /**
    * Load models into a controller
    *
    * @param string $models Models paths comma separated
    *
    * @throws IZY_Exception
    *
    * @return void Adding models instances as controller attributes
    */
    public function model($models = '')
    {
        try {
            // Not string ?
            if (gettype($models) !== 'string')
            {
                throw new IZY_Exception('L\'argument de la méthode $this->load->model() doit être une chaîne de caractères.', 1);
                die;
            }
            
            $models = explode(',', str_replace(' ', '', $models));
        }
        catch (IZY_Exception $e)
        {
          echo $e;
        }

        foreach($models as $model)
        {
            $class =& load_model(str_replace('/', DIRECTORY_SEPARATOR, $model));
            $var = str_replace('/', '_', strtolower($model));

            get_instance()->$var = $class;
        }
    }
}
