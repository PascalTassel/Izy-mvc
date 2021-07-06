<?php
namespace core\libraries;

if(!defined('IZY')) die('DIRECT ACCESS FORBIDDEN');

/**
* Pagination component
* @author https://www.izy-mvc.com
*/
class IZY_Pagination
{
    protected $_alias = 'page';     // Var name in query url
    protected $_limit = 20;         // Limit
    protected $_range = 2;          // Nb prevs et nexts links in pagination
    protected $_source = 'query';   // Datas reception mode url|query
    protected $_page = 1;           // Current page
    protected $_count = 1;          // Total pages
    protected $_links = [];         // Pagination links
    protected $_base_url;           // Base url of links
    protected $_query_string;       // Request query string

    public function __construct(array $settings = [])
    {
        // Settings
        foreach($settings as $attribute => $value)
        {
            $method = 'set_' . $attribute;
            if(is_callable(array($this, $method))){
                $this->$method($value);
            }
        }
        
        $this->IZY =& get_instance();
    }
    
    public function set_alias(string $alias)
    {
        $this->_alias = $alias === '' ? $this->_alias : $alias;
    }
    
    public function set_limit(int $limit)
    {
        $this->_limit = $limit <= 0 ? $this->_limit : $limit;
    }
    
    public function set_range(int $range)
    {
        $this->_range = $range;
    }
    
    public function set_source(string $source)
    {
        $sources = ['url', 'query'];
        
        $this->_source = in_array($source, $sources) ? $source : $this->_source;
    }
    
    public function get_alias()
    {
        return $this->_alias;
    }
    
    public function get_count()
    {
        return $this->_count;
    }
    
    public function get_limit()
    {
        return $this->_limit;
    }
    
    public function get_links()
    {
        return $this->_links;
    }
    
    public function get_page()
    {
        return $this->_page;
    }
    
    public function get_range()
    {
        return $this->_range;
    }
    
    public function get_source()
    {
        return $this->_source;
    }

    /**
    * Set pagination and canonicals links
    * @param int $total Total items to display
    * @return int $page Current page number
    */
    public function set_links(int $total, int $page)
    {
        // Base url
        $this->_set_base_url();

        // Page
        $this->_page = $page;

        // Count
        $this->_count = ceil($total / $this->_limit);

        // First
        $this->_links['first'] = ($this->_page - 1) > 1 ? ['page' => 1, 'url' => $this->_set_link(1)] : NULL;

        // Prev
        $this->_links['prev'] = $this->_page > 1 ? ['page' => $this->_page, 'url' => $this->_set_link($this->_page - 1)] : NULL;

        // Prevs
        $this->_links['prevs'] = $this->_page > 1 ? [] : NULL;

        if($this->_page > 1)
        {
            $this->IZY->output->add_canonical('prev', $this->_set_link($page - 1));

            $from = ($this->_page - $this->_range) <= 0 ? 1 : $this->_page - $this->_range;
            for($from; $from < $this->_page; $from ++)
            {
                array_push($this->_links['prevs'], ['page' => $from, 'url' => $this->_set_link($from)]);
            }
        }

        // Self
        $this->_links['self'] = ['page' => $this->_page, 'url' => $this->_set_link($this->_page)];
        $this->IZY->output->add_canonical('canonical', $this->_set_link($page));

        // Nexts
        $this->_links['nexts'] = $this->_page < $this->_count ? [] : NULL;

        if($this->_page < $this->_count)
        {
            $this->IZY->output->add_canonical('next', $this->_set_link($page + 1));

            $to = ($this->_page + $this->_range) > $this->_count ? $this->_count : ($this->_page + $this->_range);
            for($from = ($this->_page + 1); $from <= $to; $from ++)
            {
                array_push($this->_links['nexts'], ['page' => $from, 'url' => $this->_set_link($from)]);
            }
        }

        // Next
        $this->_links['next'] = $this->_page < $this->_count ? ['page' => $this->_page + 1, 'url' => $this->_set_link($this->_page + 1)] : NULL;

        // Last
        $this->_links['last'] = ($this->_page + 1) < $this->_count ? ['num' => $this->_count, 'url' => $this->_set_link($this->_count)] : NULL;
    }

    /**
    * Set link's base url
    */
    protected function _set_base_url()
    {
        // Base url
        $this->_base_url = $this->IZY->url->get_request();
        
        // Query string
        $this->_query_string = http_build_query($this->IZY->url->get_queries(), '', '&amp;');

        // Get current page number
        // From Url
        if($this->_source == 'url')
        {
            preg_match_all('/\/(' . $this->_alias . '\/[0-9]+)/', $this->_base_url, $matches, PREG_SET_ORDER, 0);

            foreach($matches as $match)
            {
                $this->_page = (int) $match[0];
            }

            //Remove page and limit from url
            $this->_base_url = preg_replace('/\/(' . $this->_alias . '\/[0-9]+)/', '', $this->_base_url);
        }
        // From query
        else if($this->_query_string !== '')
        {
            // Get queries
            $queries = $this->IZY->url->get_queries();

            $this->_page = isset($queries[$this->_alias]) ? (int) $queries[$this->_alias] : $this->_page;

            //Remove page and limit from query
            unset($queries[$this->_alias]);
            $this->_query_string = http_build_query($queries, '', '&amp;');
        }
    }

    /**
    * Set a pagination link
    * @param int $page_num Page number of the link
    * @return string Link's url
    */
    protected function _set_link($page_num)
    {
        if($this->_source == 'query')
        {
            $link_url = $this->_base_url . ($page_num > 1 ? '?' . $this->_alias . '=' . $page_num : '');
            $link_url .= !empty($this->_query_string) ? ($page_num > 1 ? '&amp;' : '?') . $this->_query_string : '';
        }
        else
        {
            $link_url = $this->_base_url . ($page_num > 1 ? '/' . $this->_alias .'/' . $page_num : '');
            $link_url .= !empty($this->_query_string) ? '?' . $this->_query_string : '';
        }
        
        return $link_url;
    }
}
