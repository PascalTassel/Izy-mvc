<?php
namespace core\helpers;

if(!defined('IZY')) die('DIRECT ACCESS FORBIDDEN');

/**
* URL helper
* @author https://www.izy-mvc.com
*/
class IZY_Url_helper
{
    public function __construct()
    {

    }

    public function check_queries($rules)
    {
        // Copie des variables $_GET
        $q = $this->get_queries();

        // Stockage de l'ordre correct des variables
        $vars_order = array_flip(array_keys($rules));

        foreach($rules as $name => $value)
        {
            // Dépendances, si la variable est dépendante
            if(isset($q[$name]) && isset($value['depends']))
            {
                foreach($value['depends'] as $field)
                {
                    // Si la variable cible n'existe pas, on la crée
                    if(!isset($q[$field]))
                    {
                        $q[$field] = '';
                    }
                }
            }

            // Variables manquantes ?
            if(!isset($q[$name]))
            {
                if(isset($value['required']) && $value['required'] === TRUE)
                {
                    $q[$name] = '';
                }
                else if(!isset($value['required']) || $value['required'] === FALSE)
                {
                    unset($vars_order[$name]);
                }
            }
        }

        foreach($q as $name => $value)
        {
            // Si le paramètre est autorisé
            if(isset($rules[$name]))
            {
                // Valeur string correcte ?
                if($rules[$name]['type'] === 'string')
                {
                    $value = empty($value) ? '' : $value;
                    $value = strval($value);

                    if(isset($rules[$name]['accept']))
                    {
                        if(gettype($rules[$name]['accept']) == 'array')
                        {
                            $q[$name] = !in_array($value, $rules[$name]['accept']) ? $rules[$name]['accept'][0] : $value;
                        }
                        else
                        {
                            $q[$name] = $value != $rules[$name]['accept'] ? $rules[$name]['accept'] : $value;
                        }
                    }
                }
                // Valeur de type int
                else if($rules[$name]['type'] === 'int')
                {
                    $value = empty($value) ? 0 : $value;
                    $value = intval($value);

                    if(isset($rules[$name]['range']))
                    {
                        if($value <= $rules[$name]['range'][0])
                        {
                            $q[$name] = strval($rules[$name]['range'][0]);
                        }
                        else if($value > $rules[$name]['range'][1])
                        {
                            $q[$name] =  strval($rules[$name]['range'][1]);
                        }
                        else if(($value % $rules[$name]['range'][0]) != 0)
                        {
                            $q[$name] =  strval($rules[$name]['range'][0]);
                        }
                    }
                    else if(isset($rules[$name]['min']) && ($q[$name] < $rules[$name]['min']))
                    {
                            $q[$name] = strval($rules[$name]['min']);
                    }
                    else if(isset($rules[$name]['max']) && ($q[$name] > $rules[$name]['max']))
                    {
                    $q[$name] = strval($rules[$name]['max']);
                    }
                }
            }
            // Sinon, on le supprime
            else
            {
                unset($q[$name]);
            }
        }

        // On remet les variables dans l'ordre
        $q = array_merge($vars_order, $q);

        // Si la chaîne de sortie est différente
        if(http_build_query($q) != $_SERVER['QUERY_STRING'])
        {
            // On recharge la page
            $url = $this->current_url() . '?' . http_build_query($q);
            get_instance()->http->redirect($url, TRUE, 301);
        }
    }

    public function current_url($path = '')
    {
        $path = (gettype($path) == 'array') && count($path) > 0 ? implode('/', $path) : $path;
        $url = get_instance()->url->request . ($path != '' ?  '/' . $path : '');

        return $this->site_url($url);
    }

    public function get_queries()
    {
        return get_instance()->url->queries;
    }

    public function query_string()
    {
        $queries = $this->get_queries;
        return http_build_query($queries, '', '&amp;');
    }

    public function segment($key = 0)
    {
        $k = (int) $key;
        $segments = $this->segments();
        return (count($segments) >= ($k + 1)) && ($segments[$k] != '') ? $segments[$k] : NULL;
    }

    public function segments()
    {
        return explode('/', get_instance()->url->request);
    }

    public function site_url($path = '')
    {
        $url = (gettype($path) == 'array') && (count($path) != 0) ? implode('/', $path) : $path;

        return get_instance()->url->protocol . '://'. get_instance()->url->host . ($path != '' ?  '/' . $path : '');
    }
}
