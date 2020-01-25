<?php

namespace core\libraries;

if(!defined("IZI")) die("DIRECT ACCESS FORBIDDEN");

/**
* Display HTML
* @author https://www.izi-mvc.com
*/
class IZI_Output
{
	private static $_buffer	= "";								// Content to display
	private static $_layout = [									// Layout datas
		"canonicals" => [],
		"name" => null
	];

	private static $_view = null;								// View to display
	private static $_size = 0;									// Output size

	public static function get_size()
	{
		return round((self::$_size / 1024), 2);
	}

  /**
  * Défine a layout canonical tag
  * @param string $name Meta name
  * @param string $link Meta link
  */
  public static function set_canonical($name, $link)
	{
		try
		{
			if(!in_array($name, ["prev", "canonical", "next"]))
			{
				throw new IZI_Exception($name . " is not an available attribute for canonical meta tag in IZI_Output::set_canonical() function.");
			}
	    self::$_layout["canonicals"][$name] = $link;
		}
		catch (IZI_Exception $e)
		{
			die($e);
		}
  }

  /**
  * Display raw content
  * @param string $content Content to display
  */
  public static function raw($content)
	{
		// Append Content to $_buffer
		self::append($content);
  }

  /**
  * Add vars to layout
  * @param array $vars Associative array
  */
	public static function set_layout($vars)
	{
    try
    {
      if(gettype($vars) != "array")
      {
        throw new IZI_Exception("Argument of IZI_Output::set_layout() function must be an array.");
      }

			self::$_layout = array_merge(self::$_layout, $vars);
    }
    catch (IZI_Exception $e)
    {
      die($e);
    }
	}

  /**
  * Get HTML view
  * @param string $view View name
  * @param array $datas Data's view
  * @param boolean $raw Return HTML or append in HTML buffer
  */
  public static function set_view($view, $datas = [], $raw = FALSE)
	{
    self::$_view = $view;

	  $content = IZI_Load::view(self::$_view, $datas);

		if($raw)
		{
			return $content;
		}

		// Append HTML view to $_buffer
		self::append($content);
  }

	/**
  * Display view
	* @return string Output HTML
  */
	public static function display()
	{
		$output =  self::$_buffer;

		if(!is_null(self::$_layout["name"]) && !is_null(self::$_view))
		{
			// Get layout keys as vars
			extract(self::$_layout);

	    // HTML view for layout
			define("OUTPUT", $output);

			try
	    {
	      if(!is_file(LAYOUT_PATH . self::$_layout["name"] . ".php"))
	      {
	        throw new IZI_Exception("Layout " . LAYOUT_PATH . self::$_layout["name"] . ".php not found.");
	      }
		    // Launch cache
		    ob_start();

		    // Display layout
		    include(LAYOUT_PATH . self::$_layout["name"] . ".php");

		    // Get HTML
		    $output = ob_get_contents();

				// Close cache
				ob_end_clean();
	    }
	    catch (IZI_Exception $e)
	    {
	      die($e);
	    }
		}

		// Content size
		self::$_size = mb_strlen($output);

		// HTML made with PHP, no cache
		IZI_Http::add_header("Expires: 0");
		IZI_Http::add_header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		IZI_Http::add_header("Cache-Control: no-store, no-cache, must-revalidate");

		IZI_Http::send_headers();

		// Display
		echo $output;

		// Post_display hook
		IZI_Load::hook("post_display");
	}

	public static function append($output)
	{
		self::$_buffer .= $output;
	}

	public static function flush()
	{
		while(ob_get_level()>0)
		{
			ob_end_clean();
		}
	}
}
