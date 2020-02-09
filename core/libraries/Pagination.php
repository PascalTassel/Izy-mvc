<?php
namespace core\libraries;

if(!defined('IZY')) die('DIRECT ACCESS FORBIDDEN');

/**
* Pagination component
* @author https://www.izi-mvc.com
*/
class IZY_Pagination extends IZY_Library
{
  // Default settings
  public $limit = 20;           // Limit
  public $range = 2;            // Nb prevs et nexts links
  public $alias = 'page';       // Alias $page in url
  public $source = 'query';     // Datas reception mode url|query

  // Default values
  public $page = 1;            // Current page
  public $count = 1;           // Total pages
  public $links = [];          // Pagination links

  // Protected variables
  protected $_base_url;
  protected $_query_string;

  public function __construct()
  {
    // Get config settings
    parent::__construct('Pagination');

    // Base url
    $this->_base_url = get_instance()->url->request;
  }

  /**
  * Set pagination and canonicals links
  */
  public function set_links(int $total, int $page)
  {
    // Base url
    $this->_set_base_url();

    // Page
    $this->page = $page;

    // Count
    $this->count = ceil($total / $this->limit);

    // First
    $this->links['first'] = ($this->page - 1) > 1 ? ['page' => 1, 'url' => $this->_set_link(1)] : NULL;

    // Prev
    $this->links['prev'] = $this->page > 1 ? ['page' => $this->page, 'url' => $this->_set_link($this->page - 1)] : NULL;

    // Prevs
    $this->links['prevs'] = $this->page > 1 ? [] : NULL;

    if($this->page > 1)
    {
      get_instance()->output->canonical('prev', $this->_base_url . (($this->page - 1) > 1 ? '?' . $this->alias . '=' . ($this->page - 1) : ''));

      $from = ($this->page - $this->range) <= 0 ? 1 : $this->page - $this->range;
      for($from; $from < $this->page; $from ++)
      {
        array_push($this->links['prevs'], ['page' => $from, 'url' => $this->_set_link($from)]);
      }
    }

    // Self
    $this->links['self'] = ['page' => $this->page, 'url' => $this->_set_link($this->page)];
    get_instance()->output->canonical('canonical', $this->_base_url . ($this->page > 1 ? '?' . $this->alias . '=' . $this->page : ''));

    // Nexts
    $this->links['nexts'] = $this->page < $this->count ? [] : NULL;

    if($this->page < $this->count)
    {
      get_instance()->output->canonical('next', $this->_base_url . '?page=' . ($this->page + 1));

      $to = ($this->page + $this->range) > $this->count ? $this->count : ($this->page + $this->range);
      for($from = ($this->page + 1); $from <= $to; $from ++)
      {
        array_push($this->links['nexts'], ['page' => $from, 'url' => $this->_set_link($from)]);
      }
    }

    // Next
    $this->links['next'] = $this->page < $this->count ? ['page' => $this->page + 1, 'url' => $this->_set_link($this->page + 1)] : NULL;

    // Last
    $this->links['last'] = ($this->page + 1) < $this->count ? ['num' => $this->count, 'url' => $this->_set_link($this->count)] : NULL;
  }

  protected function _set_base_url()
  {
    // Query string
    $this->_query_string = http_build_query(get_instance()->url->queries, '', '&amp;');

    // Extract page and limit
    // From Url
    if($this->source == 'url')
    {
      preg_match_all('/\/(' . $this->alias . '\/[0-9]+)/', $this->_base_url, $matches, PREG_SET_ORDER, 0);

      foreach($matches as $match)
      {
        $this->page = (int) $match[2];
      }

      //Remove page and limit from url
      $this->_base_url = preg_replace('/\/(' . $this->alias . '\/[0-9]+)/', '', $this->_base_url);
    }
    // From query
    else if($this->_query_string !== '')
    {
      // Get queries
      $queries = get_instance()->url->queries;

      $this->page = isset($queries[$this->alias]) ? (int) $queries[$this->alias] : $this->page;

      //Remove page and limit from query
      unset($queries[$this->alias]);
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
    if($this->source == 'query')
    {
      $link_url = $this->_base_url . ($page_num > 1 ? '?' . $this->alias . '=' . $page_num : '');
      $link_url .= !empty($this->_query_string) ? ($page_num > 1 ? '&amp;' : '?') . $this->_query_string : '';
    }
    else
    {
      $link_url = $this->_base_url . '/' . $this->alias .'/' . $page_num;
      $link_url .= !empty($this->_query_string) ? '?' . $this->_query_string : '';
    }

    return $link_url;
  }
}
