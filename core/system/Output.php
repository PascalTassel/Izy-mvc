<?php
namespace core\system;

if(!defined('IZY')) die('DIRECT ACCESS FORBIDDEN');

/**
* Display Output datas
* @author https://www.izy-mvc.com
*/
class IZY_Output
{
    private static $_output	= '';                   // Response content
    private static $_time_taken = 0;                // Time load
    private static $_start_time;                    // Init time load
    private static $_layout = [];                   // Layout datas
    public $canonicals = [];                        // Canonicals metas
    
    public function __construct($response_controller = '')
    {
        self::$_start_time = microtime(TRUE);
    }

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
        try {
            // Isset $config ?
            if (!in_array($rel, ['prev', 'canonical', 'next']))
            {
                throw new \core\system\IZY_Exception($rel . ' n\'est pas un attribut canonical valide.', 1);
                die;
            }

            $this->canonicals[$rel] = $link;
        }
        catch (\core\system\IZY_Exception $e)
        {
          echo $e;
        }
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
        try {
            if (gettype($datas) != 'array')
            {
                throw new \core\system\IZY_Exception('L\'argument passé au layout doit être un tableau associatif.', 1);
                die;
            }
            elseif (isset($datas['path']) && gettype($datas['path']) != 'string')
            {
                throw new \core\system\IZY_Exception('Le chemin du layout doit être une chaîne de caractères.', 1);
                die;
            }

            self::$_layout = array_merge(self::$_layout, $datas);
        }
        catch (\core\system\IZY_Exception $e)
        {
          echo $e;
        }
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
    * Get output length
    */
    public function get_length()
    {
        return ob_get_length();
    }

    /**
    * Get output time loaded
    */
    public function get_time()
    {
        return self::$_time_taken;
    }

    /**
    * Display view
    * @return string Output HTML
    */
    public function _display()
    {
        if(isset(self::$_layout['path']))
        {
            try {
                if (!is_file(APP_PATH . self::$_layout['path'] . '.php'))
                {
                    throw new \core\system\IZY_Exception('Layout ' . APP_PATH . self::$_layout['path'] . '.php introuvable.');
                    die;
                }
                
                // Get layout keys as vars
                extract(self::$_layout);
            }
            catch (\core\system\IZY_Exception $e)
            {
              echo $e;
            }
            
            // Launch cache
            ob_start();
            
            // End time loading
            self::$_time_taken = (microtime(TRUE) - self::$_start_time);

            // Display layout
            include(APP_PATH . self::$_layout['path'] . '.php');

            // Get HTML
            self::$_output = ob_get_contents();

            // Close cache
            ob_end_clean();
        }
        else
        {
            // End time loading
            self::$_time_taken = (microtime(TRUE) - self::$_start_time);
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
