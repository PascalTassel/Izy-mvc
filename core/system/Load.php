<?php
namespace core\system;

if(!defined('IZY')) die('DIRECT ACCESS FORBIDDEN');

/**
* Load a helper or a model in a controller
*
* @package Izy-mvc
* @copyright 2021 © Pascal Tassel for https://www.izy-mvc.com <contact[@]izy-mvc.com>
*/
class IZY_Load
{
    /**
    * Load helper in a controller
    *
    * @param string $helper Helper path
    * @param string $alias Attribute name to access helper
    *
    * @throws IZY_Exception
    *
    * @return void Adding helper instance as controller attribute
    */
    public function helper($helper, $alias = '')
    {
        try {
            // Helper isn't string ?
            if (gettype($helper) !== 'string')
            {
                throw new IZY_Exception('L\'argument de la méthode IZY_load->helper() doit être une chaîne de caractères.', 1);
                die;
            }
            // Alias isn't string ?
            else if (gettype($alias) !== 'string')
            {
                throw new IZY_Exception('L\'alias du helper ' . $helper . ' doit être une chaîne de caractères.');
                die;
            }
            
            $attribute = (trim($alias) !== '') ? trim($alias) : strtolower($helper);
        }
        catch (IZY_Exception $e)
        {
          echo $e;
        }
        
        // Access to the helper as an attribute of the controller
        get_instance()->$attribute =& load_class(str_replace('/', DIRECTORY_SEPARATOR, $helper), 'helpers');
    }
    
    /**
    * Load model in a controller
    *
    * @param string $model Model path
    * @param string $alias Attribute name to access model
    *
    * @throws IZY_Exception
    *
    * @return void Adding model instance as controller attribute
    */
    public function model($model, $alias = '')
    {
        try {
            // Model isn't string ?
            if (gettype($model) !== 'string')
            {
                throw new IZY_Exception('L\'argument de la méthode IZY_load->model() doit être une chaîne de caractères.');
                die;
            }
            // Alias isn't string ?
            else if (gettype($alias) !== 'string')
            {
                throw new IZY_Exception('L\'alias du modèle ' . $model . ' doit être une chaîne de caractères.');
                die;
            }
            
            $attribute = (trim($alias) !== '') ? trim($alias) : str_replace('/', '_', strtolower($model));
        }
        catch (IZY_Exception $e)
        {
          echo $e;
        }

        // Access to the helper as an attribute of the controller
        get_instance()->$attribute =& load_model(str_replace('/', DIRECTORY_SEPARATOR, $model));
    }
}
