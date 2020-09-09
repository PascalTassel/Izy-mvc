<?php
namespace core\libraries;

if(!defined('IZY')) die('DIRECT ACCESS FORBIDDEN');

/**
* Pagination component
* @author https://www.izy-mvc.com
*/
class IZY_Breadcrumb
{
    // Default settings
    public $segments = [];            // Breadcrumb segments

    public function __construct(array $settings)
    {
        // Settings
        foreach($settings as $attribute => $value)
        {
            $this->$attribute = $value;
        }
    }

    /**
    * Add a segment in breadcrumb
    * @param string $label Segment label
    * @param string $url Segment url
    */
    public function add_segment(string $label, string $url = '')
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
