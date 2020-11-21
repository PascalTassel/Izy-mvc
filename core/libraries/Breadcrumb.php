<?php
namespace core\libraries;

if(!defined('IZY')) die('DIRECT ACCESS FORBIDDEN');

/**
* Pagination component
* @author https://www.izy-mvc.com
*/
class IZY_Breadcrumb
{
    protected $_home = 'Home';  // Root label
    
    /**
    * Define settings
    * @param array $settings Settings array
    */
    public function __construct(array $settings = [])
    {
        // Settings
        foreach($settings as $attribute => $value)
        {
            $attribute = '_' . $attribute;
            
            if(property_exists($this, $attribute))
            {
                $this->$attribute = $value;
            }
        }
        
        $this->segments = [];
        $this->IZY =& get_instance();
    }

    /**
    * Add a segment in breadcrumb
    * @param string $label Segment label
    * @param string $url Segment url
    */
    public function add_segment(string $label = '', string $url = '')
    {
        $this->segments[$label] = $url;
    }

    /**
    * Get breadcrumb's segments
    */
    public function get_segments()
    {    
        if((gettype($this->_home) === 'string') && ($this->_home !== ''))
        {
            $this->segments = array($this->_home => $this->IZY->url_helper->site_url()) + $this->segments;
        }
        return $this->segments;
    }
}
