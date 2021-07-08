<?php
namespace core\libraries;

/**
* Build and write response
*
* @package Izy-mvc
* @copyright 2021 © Pascal Tassel for https://www.izy-mvc.com <contact[@]izy-mvc.com>
*/
class IZY_Breadcrumb
{
    private static $_home = 'Home';  // Root label
    private static $_root = '';      // Root url
    
    /**
    * Define settings
    * 
    * @param array $settings Settings array
    */
    public function __construct(array $settings = [])
    {
        // Settings
        foreach($settings as $attribute => $value)
        {
            $attribute = '_' . $attribute;
            
            // Is it a valid attribute?
            if (property_exists($this, $attribute))
            {
                
                // Is it same type
                if (gettype($value) === gettype(self::${$attribute}))
                {
                    self::${$attribute} = $value;
                }
            }
        }
        
        $this->segments = [];
        $this->IZY =& get_instance();
        
        if (self::$_home !== '')
        {
            self::$_root = (self::$_root !== '') ? self::$_root : $this->IZY->url_helper->site_url();
            $this->add_segment(self::$_home, self::$_root);
        }
    }
    
    /**
    * Add segment to breadcrumb
    *
    * @param string $label Segment label
    * @param string $url Segment url
    *
    * @throws IZY_Exception
    *
    * @return void Added segment to breadcrumb
    */
    public function add_segment($label = '', $url = '')
    {
        try {
            // Not isset $config ?
            if (gettype($label) !== 'string' || gettype($url) !== 'string')
            {
                throw new IZY_Exception('IZY_Breadcrumb->add_segment() : Paramètres incorrects.');
                die;
            }
            
            $this->segments[$label] = $url;
        }
        catch (IZY_Exception $e)
        {
          echo $e;
        }
    }
    
    /**
    * Get breadcrumb's segments
    *
    * @return array Segments as an associative array
    */
    public function get_segments()
    {
        return $this->segments;
    }
}
