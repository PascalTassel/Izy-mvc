<?php
namespace core\system;

if(!defined('IZY')) die('DIRECT ACCESS FORBIDDEN');

/**
* Build and write response
*
* @package Izy-mvc
* @copyright 2021 © Pascal Tassel for https://www.izy-mvc.com <contact[@]izy-mvc.com>
*/
class IZY_Output
{
    private static $_output = '';       // Response content
    private static $_time_taken = 0;    // Time load
    private static $_start_time = 0;    // Init time load
    private static $_layout = [];       // Layout datas
    private static $_canonicals = [];   // Canonicals metas
    
    /**
    * Init $_start_time attribute
    *
    * @return void Attribute definition
    */
    public function __construct()
    {
        self::$_start_time = microtime(TRUE);
    }
    
    
    /**
    * Get and set attributes
    *
    * @param string $method Function name
    * @param string $value Function name
    *
    * @return mixed|void
    */
    public function __call($method, $value)
    {
        $attribute = '_' . lcfirst(substr($method, 4));
        
        // Is it a valid attribute
        if (property_exists($this, $attribute))
        {
            // Getter
            if (strncasecmp($method, 'get_', 4) === 0)
            {
                return self::${$attribute};
                
            // Setter
            } else if (strncasecmp($method, 'set_', 4) === 0) {
                
                // Is it same type
                if (gettype($value) === gettype($this->$attribute))
                {
                    self::${$attribute} = $value;
                }
            }
        }
    }

    /**
    * Add content to output
    *
    * @param string $content Content to add
    */
    public function append($content = '')
    {
        self::$_output .= $content;
    }
    
    /**
    * Add canonical meta
    *
    * @param string $rel Value of the rel attribute
    * @param string $link Value of the href attribute
    *
    * @throws IZY_Exception
    *
    * @return void Canonical meta added to $canonicals array
    */
    public function add_canonical($rel, $link)
    {
        try {
            // Not isset $config ?
            if (!in_array($rel, ['prev', 'canonical', 'next']))
            {
                throw new IZY_Exception($rel . ' n\'est pas un attribut canonical valide.', 1);
                die;
            }

            $this->canonicals[$rel] = $link;
        }
        catch (IZY_Exception $e)
        {
          echo $e;
        }
    }
    
    /**
    * Remove output content
    *
    * @return void Empty output
    */
    public function empty()
    {
    	self::$_output = '';
    }
    
    /**
    * Get output content
    *
    * @return string Content of output (html, json, etc.)
    */
    public function get_content()
    {
        return self::$_output;
    }
    
    /**
    * Get response length
    *
    * @return float Output length in octets
    */
    public function get_length()
    {
        return ob_get_length();
    }

    /**
    * Get response processing time
    *
    * @return float Response processing time
    */
    public function get_time()
    {
        return self::$_time_taken;
    }
    
    /**
    * Add datas to layout
    *
    * @param array $datas Associative array of datas intended for layout
    *
    * @throws IZY_Exception
    *
    * @return void Datas added into $layout array
    */
    public function layout($datas)
    {
        try {
            // It's not an array ?
            if (gettype($datas) !== 'array')
            {
                throw new IZY_Exception('L\'argument passé au layout doit être un tableau associatif.', 1);
                die;
            }

            self::$_layout = array_merge(self::$_layout, $datas);
        }
        catch (IZY_Exception $e)
        {
          echo $e;
        }

        self::$_layout = array_merge(self::$_layout, $datas);
    }
    
    /**
    * Set output view
    *
    * @param string $view Path to view file
    * @param array $datas Associative array of datas intended for view
    *
    * @return void Canonical meta added to $canonicals array
    */
    public function view($view, $datas = [])
    {
        $instance =& get_instance();
        
        // Call Pre_view class
        $instance->hooks->set_hook('pre_view');

        // Get content view
        $content = view($view, $datas);
        
        // Add content to output
        $this->append($content);

        // Call Post_view class
        $instance->hooks->set_hook('post_view');
    }

    /**
    * Write response
    *
    * @return string Output content
    */
    public function _display()
    {
        $instance =& get_instance();
        
        // Is there a layout
        if (!empty(self::$_layout))
        {
            try {
                // No path?
                if (!isset(self::$_layout['path']))
                {
                    throw new IZY_Exception('Chemin du layout non renseigné : $this->output->layout([\'path\' => \'path/to/layout\']).', 1);
                    die;
                }
                // Not string?
                else if (gettype(self::$_layout['path']) !== 'string')
                {
                    throw new IZY_Exception('Le chemin du layout doit être une chaîne de caractères.', 1);
                    die;
                }
            }
            catch (IZY_Exception $e)
            {
              echo $e;
            }
            
            try {
                // Layout not found ?
                if (!is_file(APP_PATH . (str_replace('/', DIRECTORY_SEPARATOR, self::$_layout['path'])) . '.php'))
                {
                    throw new IZY_Exception('Layout ' . APP_PATH . self::$_layout['path'] . '.php introuvable.');
                    die;
                }
                
                // Get layout datas
                extract(self::$_layout);
            }
            catch (IZY_Exception $e)
            {
              echo $e;
            }
            
            // Launch cache
            ob_start();

            // Include layout file
            include(APP_PATH . (str_replace('/', DIRECTORY_SEPARATOR, self::$_layout['path'])) . '.php');

            // Get content
            self::$_output = ob_get_contents();

            // Close cache
            ob_end_clean();
        }
        
        // End time loading
        self::$_time_taken = (microtime(TRUE) - self::$_start_time);

        // Add HTTP headers : HTML made with PHP, no cache
        $instance->http->add_header('Expires: 0, false');
        $instance->http->add_header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT, false');
        $instance->http->add_header('Cache-Control: no-store, no-cache, must-revalidate, false');
        
        // Send headers
        $instance->http->send_headers();

        // Write response
        echo self::$_output;
    }
}
