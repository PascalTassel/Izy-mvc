<?php

namespace core\libraries;

if(!defined('IZI')) die('DIRECT ACCESS FORBIDDEN');

/**
* Display HTML
* @author https://www.izi-mvc.com
*/
class IZI_Output
{
	private static $_output	= '';							// Response content
	private static $_layout = [];							// Layout datas
	private static $_canonicals = [];					// Canonicals metas
	private static $_view = '';								// View to display

  /**
  * Défine a layout canonical tag
  * @param string $name Meta name
  * @param string $link Meta link
  */
  public static function set_canonical($rel, $link)
	{
 		try
		{
			if(!in_array($rel, ['prev', 'canonical', 'next']))
			{
				throw new IZI_Exception($rel . ' is not an available attribute for canonical meta tag.');
			}
			else if(($link == '') || (gettype($link) != 'string'))
			{
				throw new IZI_Exception('$link is empty in canonical meta tag.');
			}

	    self::$_canonicals[$rel] = $link;
		}
		catch (IZI_Exception $e)
		{
			die($e);
		}
  }

  /**
  * Add vars to layout
  * @param array $datas Layout datas
  */
	public static function set_layout($datas)
	{
    try
    {
      if(gettype($datas) != 'array')
      {
        throw new IZI_Exception('Argument of set_layout() must be an array.');
      }
			elseif(isset($datas['path']) && gettype($datas['path']) != 'string')
			{
				throw new IZI_Exception('Layout path must be a string.');
			}

			self::$_layout = array_merge(self::$_layout, $datas);
    }
    catch (IZI_Exception $e)
    {
      die($e);
    }
	}

  /**
  * Add content to output
  * @param string $content Content to add
  */
  public static function set_output($content = '')
	{
		try
		{
			if(gettype($content) != 'string')
			{
				throw new IZI_Exception('Invalid $output type. Adding to $output failed.');
			}

			// Append content to $output
			self::$_output .= $content;
		}
		catch (IZI_Exception $e)
		{
			die($e);
		}
  }

  /**
  * Get HTML view
  * @param string $path View path
  * @param array $datas Data's view
  * @param boolean $return Return view content
  */
  public static function set_view($path, $datas = [], $return = FALSE)
	{
    self::$_view = $path;

	  $content = IZI_Load::view(self::$_view, $datas);

		if($return)
		{
			return $content;
		}

		// Append HTML view to $output
		self::set_output($content);
  }

  public static function get_output()
	{
		return self::$_output;
  }

  public static function get_canonicals()
	{
		return self::$_canonicals;
  }

	/**
  * Display view
	* @return string Output HTML
  */
	public static function _display()
	{
		if(isset(self::$_layout['path']) && (self::$_view != ''))
		{
			// Get layout keys as vars
			extract(self::$_layout);

	    // HTML view for layout
			define('OUTPUT', self::$_output);

			try
	    {
	      if(!is_file(APP_PATH . self::$_layout['path'] . '.php'))
	      {
	        throw new IZI_Exception('Layout ' . APP_PATH . self::$_layout['path'] . '.php not found.');
	      }
		    // Launch cache
		    ob_start();

		    // Display layout
		    include(APP_PATH . self::$_layout['path'] . '.php');

		    // Get HTML
		    self::$_output = ob_get_contents();

				// Close cache
				ob_end_clean();
	    }
	    catch (IZI_Exception $e)
	    {
	      die($e);
	    }
		}

		// HTML made with PHP, no cache
		IZI_Http::add_header('Expires: 0');
		IZI_Http::add_header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		IZI_Http::add_header('Cache-Control: no-store, no-cache, must-revalidate');

		IZI_Http::send_headers();

		// Display
		echo self::$_output;

		// Post_display hook
		IZI_Load::hook('post_system');
	}

	public static function _flush()
	{
		while(ob_get_level() > 0)
		{
			ob_end_clean();
		}
	}
}
