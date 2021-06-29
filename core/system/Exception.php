<?php
namespace core\system;

if(!defined('IZY')) die('DIRECT ACCESS FORBIDDEN');

/**
* Customized Exception
* @author Pascal Tassel : https://www.izy-mvc.com
*/
class IZY_Exception extends \ErrorException
{
    public function __construct($message, $code = 0)
    {
        parent::__construct($message, $code);
    }

    public function __toString()
    {
        switch ($this->severity)
        {
          case E_USER_ERROR :
            $this->type = 'Erreur fatale';
            break;
          
          case E_WARNING :
          case E_USER_WARNING :
            $this->type = 'Attention';
            break;
          
          case E_NOTICE :
          case E_USER_NOTICE :
            $this->type = 'Note';
            break;
          
          default :
            $this->type = 'Erreur';
            break;
        }
        
        return $this->_display();
    }
    
    private function _display() {
        return '
        <!DOCTYPE html>
        <html>
            <head>
                <meta charset="utf-8">
                <title>Izy-mvc : Exception attrapée</title>
            </head>
            <body>
                <header>
                    <a href="https://www.izy-mvc.com/">
                        <img src="https://www.izy-mvc.com/assets/im/brand.png">
                    </a>
                    <a href="https://www.izy-mvc.com/userguide">Guide de l\'utilisateur</a>
                </header>
                <main>
                    <section>
                        <h1>' . $this->type . ' : exception rencontrée</h1>
                        <div>' . $this->message . '</div>
                        <small>Exception attrapée dans le fichier : ' . $this->file . '</strong>, ligne ' . $this->line . '</small>.
                    </section>
                </main>
                
                <style type="text/css">
                    html {
                        height: 100%;
                    }
                    body {
                        height: 100%;
                        font-family: sans-serif;
                        background-color: #181826;
                        color: #a5a5ac;
                        margin: 0;
                    }
                    a {
                        color: #e14eca;
                    }
                    a:hover,
                    a:focus {
                        color: #ad3d9c;
                    }
                    h1 {
                        color: #fff;
                        margin-top: 0;
                    }
                    header {
                        position: fixed;
                        top: 0;
                        right: 0;
                        left: 0;
                        padding: .5rem;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        background-color: rgb(0 0 0 / 20%);
                    }
                    main {
                        display: flex;
                        height: 100%;
                        align-items: center;
                        justify-content: center;
                    }
                    section {
                        max-width: 50rem;
                        margin: 1rem;
                        padding: 1rem;
                        border-radius: .25rem;
                        box-shadow: 0 0 10px 10px rgb(0 0 0 / 10%);
                        background-color: #32324e;
                        text-align: center;
                    }
                    section div {
                        margin: 2rem 0;
                        font-size: 1.2rem;
                    }
                </style>
            </body>
        </html>';
    }
}
