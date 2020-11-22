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
    protected $_root = '';      // Root url
    
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
        
        if($this->_home !== '')
        {
            $this->_root = ($this->_root !== '') ? $this->_root : $this->IZY->url_helper->site_url();
            $this->add_segment($this->_home, $this->_root);
        }
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
        return $this->segments;
    }
}
