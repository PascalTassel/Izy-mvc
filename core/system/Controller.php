<?php
namespace core\system;

if(!defined('IZY')) die('DIRECT ACCESS FORBIDDEN');

/**
* Main controller
*
* @package Izy-mvc
* @copyright 2021 © Pascal Tassel for https://www.izy-mvc.com <contact[@]izy-mvc.com>
*/
class IZY_Controller
{
    private static $_instance;  // This
    
    /**
    * Define controller attributes
    *
    * @return void Attributes definition
    */
    public function __construct()
    {
        self::$_instance =& $this;

        // Assign Izy classses as controller attributes (excluding IZY_controller class)
        foreach (system_loaded('system') as $attribute => $system_class)
        {
            if ($attribute != 'controller')
            {
                $this->$attribute =& load_class($system_class, 'system');
            }
        }
        // Assign helpers as controller attributes
        foreach(system_loaded('helpers') as $attribute => $system_class)
        {
            $attribute = str_replace('/', '_', strtolower($attribute));
            $this->$attribute =& load_class($system_class, 'helpers');
        }

        // Assign models as model attributes
        foreach(models_loaded() as $attribute => $model)
        {
            $attribute = str_replace('/', '_', strtolower($attribute));
            $this->$attribute =& load_model($model);
        }
    }

    /**
    * Get the main controller instance
    *
    * @return object Main controller instance
    */
    public static function &get_instance()
    {
        return self::$_instance;
    }
}
