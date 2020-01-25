<?php
namespace core\components;

if(!defined("IZI")) die("DIRECT ACCESS FORBIDDEN");

class Pagination extends \core\libraries\IZI_Components
{

  private static $_get_mode = TRUE;          // Datas mode : $_GET or []
  private static $_datas = [];               // Datas
  private static $_base_url = "";            // Current url
  private static $_queries = [];             // $_GET []
  private static $_total = 0;                // Total items
  private static $_pages_count = 1;          // Total pages
  private static $_page_num = 1;             // Current page
  private static $_links = [];               // Pagination links
  private static $_limit;                    // Limit
  private static $_offset;                   // Offset
  private static $_range;                    // Nb prevs et nexts links

  public function __construct($total, $datas = [], $range = false)
  {
    parent::__construct();

    self::$_get_mode = empty($datas);
    self::$_total = (int) $total;
    self::$_range = $range ? $range : self::get_config("range");
    self::$_base_url = \core\helpers\Url_helper::current_url();
    self::$_queries = \core\libraries\IZI_Url::get_queries();
    self::$_datas = empty($datas) ? self::$_queries : $datas;
    self::$_page_num = isset(self::$_datas["page"]) ? (int) self::$_datas["page"] : self::$_page_num;
    self::$_limit = (int) isset(self::$_datas["limit"]) ? self::$_datas["limit"] : self::get_config("limit");
    self::$_pages_count = ceil(self::$_total / self::$_limit);

    if(self::$_get_mode)
    {
      unset(self::$_queries["page"]);
    }
    self::$_queries = http_build_query(self::$_queries, "", "&amp;");

    // Page num
    self::set_page_num();

    // Définition des liens
    self::set_links();
  }


  /**
  * Défini les liens self, prev et next de la page dans $canonicals
  */
  public static function get_links()
  {
    return self::$_links;
  }

  /**
  * Défini les liens self, prev et next de la page dans $canonicals
  */
  public static function get_pages_count()
  {
    return self::$_pages_count;
  }


  /**
  * Défini les liens self, prev et next de la page dans $canonicals
  */
  private static function set_page_num()
  {
    self::$_offset = (self::$_page_num - 1) * self::$_limit;
  }


  /**
  * Lien de pagination
  */
  private static function set_link(int $page_num)
  {
    if(self::$_get_mode)
    {
      $link_url = self::$_base_url . ($page_num > 1 ? "?page=" . $page_num : "");
      $link_url .= !empty(self::$_queries) ? ($page_num > 1 ? "&amp;" : "?") . self::$_queries : "";
    }
    else
    {
      $link_url = str_replace(["{limit}", "{page_num}"], [self::$_limit, self::$_page_num], self::$_base_url);
      $link_url .= !empty(self::$_queries) ? "?" . self::$_queries : "";
    }

    return $link_url;
  }

  /**
  * Liens de pagination
  * Liens self, prev et nexts canoniques
  */
  private static function set_links()
  {
    if((self::$_page_num - 1) > 1)
    {
      // First
      self::$_links["first"] = ["num" => 1, "url" => self::set_link(1)];
    }

    if(self::$_page_num > 1)
    {
      // Prev
      self::$_links["prev"] = ["num" => self::$_page_num, "url" => self::set_link(self::$_page_num - 1)];
      \core\libraries\IZI_Output::set_canonical("prev", self::$_base_url . ((self::$_page_num - 1) > 1 ? "?page=" . (self::$_page_num - 1) : ""));

      // Prevs
      self::$_links["prevs"] = [];

      $from = (self::$_page_num - self::$_range) <= 0 ? 1 : self::$_page_num - self::$_range;
      for($from; $from < self::$_page_num; $from ++)
      {
        array_push(self::$_links["prevs"], ["num" => $from, "url" => self::set_link($from)]);
      }
    }

    // Self
    self::$_links["self"] = ["num" => self::$_page_num, "url" => self::set_link(self::$_page_num)];
    \core\libraries\IZI_Output::set_canonical("canonical", self::$_base_url . (self::$_page_num > 1 ? "?page=" . self::$_page_num : ""));

    if(self::$_page_num < self::$_pages_count)
    {
      // Nexts
      self::$_links["nexts"] = [];

      $to = (self::$_page_num + self::$_range) > self::$_pages_count ? self::$_pages_count : (self::$_page_num + self::$_range);
      for($from = (self::$_page_num + 1); $from <= $to; $from ++)
      {
        array_push(self::$_links["nexts"], ["num" => $from, "url" => self::set_link($from)]);
      }
      // Next
      self::$_links["next"] = ["num" => self::$_page_num + 1, "url" => self::set_link(self::$_page_num + 1)];
      \core\libraries\IZI_Output::set_canonical("next", self::$_base_url . "?page=" . (self::$_page_num + 1));
    }

    // Last
    if((self::$_page_num + 1) < self::$_pages_count)
    {
      self::$_links["last"] = ["num" => self::$_pages_count, "url" => self::set_link(self::$_pages_count)];
    }
  }
}
