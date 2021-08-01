<?php
namespace core\helpers;

if(!defined('IZY')) die('DIRECT ACCESS FORBIDDEN');

/**
* Url utilities
*
* @package Izy-mvc
* @copyright 2021 © Pascal Tassel for https://www.izy-mvc.com <contact[@]izy-mvc.com>
*/
class IZY_Url_helper
{
    private $_IZY;
    
    public function __construct()
    {
        $this->_IZY =& get_instance();
    }
    
    /**
    * Sanitize url queries parameters
    *
    * @param array $rules Associative array contains each parameter specifications
    *
    * @return void Redirect page if query string is rewrited
    */
    public function check_queries($rules)
    {
        // Get query vars
        $q = $this->_IZY->url->get_queries();

        // Store vars order
        $vars_order = array_flip(array_keys($rules));
        
        // Check and remove wrong vars in $q
        foreach ($q as $name => $value)
        {
            // Wrong var ?
            if (!isset($rules[$name]))
            {
                unset($q[$name]);
            }
            else
            {
                // Set default value if empty
                if ($value === '' && (isset($rules[$name]['default'])))
                {
                    $value = $rules[$name]['default'];
                }
                
                // Wrong value ?
                if (preg_match('#^' . $rules[$name]['pattern'] . '$#', $value) === 0)
                {
                    $value =  isset($rules[$name]['default']) ? $rules[$name]['default'] : '';
                }
                // Number rules ?
                else if (isset($rules[$name]['range']) || isset($rules[$name]['min']) || isset($rules[$name]['max']))
                {
                    // Convert value as number
                    $value = (strpos($value, '.') !== false) ? intval($value) : floatval($value);
                    
                    // Range
                    if (isset($rules[$name]['range']))
                    {
                        if ($value < $rules[$name]['range'][0])
                        {
                            $value = $rules[$name]['range'][0];
                        }
                        else if ($value > $rules[$name]['range'][1])
                        {
                            $value = $rules[$name]['range'][1];
                        }
                        else if (($value % $rules[$name]['range'][0]) != 0)
                        {
                            $value = $rules[$name]['range'][0];
                        }
                    }
                    // Min
                    else if (isset($rules[$name]['min']) && ($value < $rules[$name]['min']))
                    {
                        $value = $rules[$name]['min'];
                    }
                    // Max
                    else if (isset($rules[$name]['max']) && ($value > $rules[$name]['max']))
                    {
                        $value = $rules[$name]['max'];
                    }
                }
                
                $q[$name] = strval($value);
            }
        }
        
        // Add required vars in $q if not exist
        foreach ($rules as $var => $params)
        {
            $isRequired = isset($params['required']) ? $params['required'] : false;
            
            // Add var if required
            if ($isRequired && !isset($q[$var]))
            {
                $q[$var] =  isset($rules[$var]['default']) ? $rules[$var]['default'] : '';
            }
            
            // Has dependancies
            if (isset($q[$var]) && isset($rules[$var]['dependancies']))
            {
                foreach ($rules[$var]['dependancies'] as $dependancy)
                {
                    // Not isset dependancy ?
                    if (!isset($q[$dependancy]))
                    {
                        $q[$dependancy] = isset($rules[$dependancy]['default']) ? $rules[$dependancy]['default'] : '';
                        
                    }
                }
            }
            
            // Is a dependancy ?
            if (!isset($q[$var]))
            {
                $isDependancy = false;
                
                foreach (array_keys($rules) as $rule)
                {
                    if (isset($q[$rule]))
                    {
                        if (isset($rules[$rule]['dependancies']) && (in_array($var, $rules[$rule]['dependancies'])))
                        {
                            $isDependancy = true;
                            break;
                        }
                    }
                }
                
                if ($isDependancy === false)
                {
                    unset($vars_order[$var]);
                }
            }
        }
        
        // Order $q vars
        $q = array_merge($vars_order, $q);

        // If query string is rewrited
        if (http_build_query($q) !== $_SERVER['QUERY_STRING'])
        {
            // Reload page
            $url = $this->current_url() . '?' . http_build_query($q);
            $this->_IZY->http->redirect($url, TRUE, 301);
        }
    }

    /**
    * Get current_url
    *
    * @param string $path Url path to add to current path
    *
    * @return string Current url
    */
    public function current_url($path = '')
    {
        $url = $this->_IZY->url->get_request() . ($path !== '' ?  '/' . $path : '');

        return $this->site_url($url);
    }
    
    /**
    * Get query string
    *
    * @return string Query string
    */
    public function query_string()
    {
        $queries = $this->_IZY->url->get_queries();
        
        return http_build_query($queries, '', '&amp;');
    }
    
    /**
    * Get url segment by index
    *
    * @param int Segment index
    *
    * @return string|null Url segment
    */
    public function segment($key = 0)
    {
        $segments = $this->segments();
        
        return (count($segments) >= ($key + 1)) && ($segments[$key] != '') ? $segments[$key] : null;
    }
    
    /**
    * Get url segments
    *
    * @return array url segments
    */
    public function segments()
    {
        return explode('/', $this->_IZY->url->get_request());
    }
    
    /**
    * Get site url
    *
    * @param string $path Url path to add to site url
    *
    * @return string Site url
    */
    public function site_url($path = '')
    {
        return $this->_IZY->url->get_protocol() . '://'. $this->_IZY->url->get_host() . ($path !== '' ?  '/' . $path : '');
    }
}
