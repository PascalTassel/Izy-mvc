<?php

namespace core\libraries;

if(!defined('IZI')) die('DIRECT ACCESS FORBIDDEN');

/**
* Customized exceptions
* @author https://www.izi-mvc.com
*/
class IZI_Exception extends \ErrorException
{
  public function __toString()
  {
		switch ($this->severity)
    {
      case E_USER_ERROR : // Fatal error
        $type = 'Fatal error';
        break;

      case E_WARNING : // Php alert
      case E_USER_WARNING : // User alert
        $type = 'Warning';
        break;

      case E_NOTICE : // Php notice
      case E_USER_NOTICE : // User notice
        $type = 'Note';
        break;

      default :
        $type = 'Unknown error';
        break;
    }

    return '<strong>' . $type . '</strong> : [' . $this->code . '] ' . $this->message . '<br /><strong>' . $this->file . '</strong> on line <strong>' . $this->line . '</strong>';
  }
}

function error_to_exception($code, $message, $file, $line)
{
  // Le code fait office de sévérité.
  // Reportez-vous aux constantes prédéfinies pour en savoir plus.
  // http://fr2.php.net/manual/fr/errorfunc.constants.php
  throw new IZI_Exception($message, 0, $code, $file, $line);
}

set_error_handler('\core\libraries\error_to_exception');
