<?php
namespace core\system;

if(!defined('IZY')) die('DIRECT ACCESS FORBIDDEN');

/**
* Load a controller at specific moment
* @author Pascal Tassel : https://www.izy-mvc.com
*/

/**
* Call hooks classes (defined in $config['hooks']) at various key points in the process
*
* @package Izy-mvc
* @copyright 2021 © Pascal Tassel for https://www.izy-mvc.com <contact[@]izy-mvc.com>
*/
class IZY_Hooks
{
    private static $_hooks = [];        // Hooks array
    private static $_names = [          // Valid hook names
        'pre_system',
        'pre_controller',
        'post_controller',
        'pre_display',
        'pre_view',
        'post_view',
        'post_display'
    ];
    
    /**
    * Get hook classes
    *
    * @throws IZY_Exception
    *
    * @return void Attribute definition
    */
    public function __construct()
    {
        // Get hooks
        if (!is_null(get_config('hooks'))) {
            
            try {
                // Isn't array ?
                if (gettype(get_config('hooks')) !== 'array')
                {
                    throw new IZY_Exception('$config[\'hooks\'] n\'est pas un tableau associatif.');
                    die;
                }
                
                self::$_hooks = get_config('hooks');
            }
            catch (IZY_Exception $e)
            {
              echo $e;
            }
            
            // Check if hooks are valids
            foreach (self::$_hooks as $name => $params) {
                
                try {
                    // Unknown hook name?
                    if (in_array($name, self::$_names) === false)
                    {
                        throw new IZY_Exception($name . 'n\'est pas un hook valide.');
                        die;
                    }
                }
                catch (IZY_Exception $e)
                {
                  echo $e;
                }
                
                try {
                    // Class isn't defined
                    if (!isset($params['class']))
                    {
                        throw new IZY_Exception('Le champ class du hook ' . $name . ' n\'est pas défini.');
                        die;
                    }
                    // Class isn't a string
                    if (gettype($params['class']) !== 'string')
                    {
                        throw new IZY_Exception('La valeur du champ class du hook ' . $name . ' doit être une chaîne de caractères.');
                        die;
                    }
                    // Isn't a class ?
                    else if (class_exists('\\' . $params['class']) === false)
                    {
                        throw new IZY_Exception('La valeur du champ class du hook ' . $name . ' n\'est pas une classe.');
                        die;
                    }
                    
                    // Store class as attribute
                    $class = '\\' . $params['class'];
                    $this->$name = new $class();
                }
                catch (IZY_Exception $e)
                {
                  echo $e;
                }
                
                try {
                    // Class isn't defined
                    if (!isset($params['method']))
                    {
                        throw new IZY_Exception('Le champ method du hook ' . $name . ' n\'est pas défini.');
                        die;
                    }
                    // Method doesn't exist?
                    else if (in_array($params['method'], get_class_methods($this->$name)) === false)
                    {
                        throw new IZY_Exception('La méthode ' . $params['method'] . ' du hook ' . $name . ' est introuvable.');
                        die;
                    }
                }
                catch (IZY_Exception $e)
                {
                  echo $e;
                }
                
                if (isset($params['args'])) {
                    try {
                        // Args Aren't array ?
                        if (gettype($params['args']) !== 'array')
                        {
                            throw new IZY_Exception('La valeur du champ args du hook ' . $name . ' n\'est pas un tableau.');
                            die;
                        }
                    }
                    catch (IZY_Exception $e)
                    {
                      echo $e;
                    }
                    
                    foreach ($params['args'] as $arg) {
                        try {
                            // Arg isn't a string?
                            if (gettype($arg) !== 'string')
                            {
                                throw new IZY_Exception('Le champ args du hook ' . $name . ' ne doit contenir que des chaînes de caractères.');
                                die;
                            }
                        }
                        catch (IZY_Exception $e)
                        {
                          echo $e;
                        }
                    }
                } else {
                    self::$_hooks[$name]['args'] = [];
                }
            }
        }
    }
    
    /**
    * Call a hook class
    *
    * @throws IZY_Exception
    *
    * @param string $hook Hook name
    *
    * @return void Call class method
    */
    public function set_hook($name)
    {
        if (property_exists($this, $name))
        {
            $method = self::$_hooks[$name]['method'];
            $args = self::$_hooks[$name]['args'];

            // Call method
            call_user_func_array(array($this->$name, $method), $args);

            // Unset class
            unset($this->$name);
        }
    }
}
