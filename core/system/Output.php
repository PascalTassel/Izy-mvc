<?php
namespace core\system;

if(!defined('IZY')) die('DIRECT ACCESS FORBIDDEN');

/**
* Display Output datas
* @author https://www.izy-mvc.com
*/
class IZY_Output
{
    private static $_output	= '';           // Response content
    private static $_layout = [];           // Layout datas
    public $canonicals = [];                // Canonicals metas

    /**
    * Add content to output
    * @param string $content Content to add
    */
    public function append($content = '')
    {
        self::$_output .= $content;
    }

    /**
    * Défine a layout canonical tag
    * @param string $name Meta name
    * @param string $link Meta link
    */
    public function canonical($rel, $link)
    {
        if(!in_array($rel, ['prev', 'canonical', 'next']))
        {
            die($rel . ' is not an available attribute for canonical meta tag.');
        }

        $this->canonicals[$rel] = $link;
    }

    /**
    * Remove content from output
    * @param string $content Content to add
    */
    public function empty()
    {
    	self::$_output = '';
    }

    /**
    * Add vars to layout
    * @param array $datas Layout datas
    */
    public function layout($datas)
    {
        if(gettype($datas) != 'array')
        {
            die('Argument of set_layout() must be an array.');
        }
        elseif(isset($datas['path']) && gettype($datas['path']) != 'string')
        {
            die('Layout path must be a string.');
        }

        self::$_layout = array_merge(self::$_layout, $datas);
    }

    /**
    * Set view
    * @param string $view View path
    * @param array $datas Data's view
    */
    public function view($view, $datas = [])
    {
        // Pre display hook
        get_instance()->hooks->set_hook('pre_view');

        // Append to $output
        $content = view($view, $datas);
        $this->append($content);

        // Post display hook
        get_instance()->hooks->set_hook('post_view');
    }

    /**
    * Get output content
    */
    public function get_content()
    {
        return self::$_output;
    }

    /**
    * Display view
    * @return string Output HTML
    */
    public function _display()
    {
        if(isset(self::$_layout['path']))
        {
            // Get layout keys as vars
            extract(self::$_layout);

            if(!is_file(APP_PATH . self::$_layout['path'] . '.php'))
            {
                die('Unable to locate ' . APP_PATH . self::$_layout['path'] . '.php.');
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

        // HTML made with PHP, no cache
        get_instance()->http->add_header('Expires: 0, false');
        get_instance()->http->add_header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT, false');
        get_instance()->http->add_header('Cache-Control: no-store, no-cache, must-revalidate, false');

        get_instance()->http->send_headers();

        // Display
        echo self::$_output;
    }
}
