<?php

namespace core\helpers;

if(!defined("IZI")) die("DIRECT ACCESS FORBIDDEN");

class Url_helper
{

  public static function get_queries()
  {
    return \core\libraries\IZI_Url::get_queries();
  }

  public static function get_segment($key = 0)
  {
    $segments = self::get_segments();
    return count($segments) >= ($key + 1) ? $segments[$key] : NULL;
  }

  public static function get_segments()
  {
    return explode("/", self::get_url());
  }

  public static function get_url()
  {
    return \core\libraries\IZI_Url::get_url();
  }

  public static function check_queries($rules)
  {
    // Copie des variables $_GET
    $q = \core\libraries\IZI_Url::get_queries();

    // Stockage de l'ordre correct des variables
    $vars_order = array_flip(array_keys($rules));

    foreach($rules as $name => $value)
    {
      // Dépendances, si la variable est dépendante
      if(isset($q[$name]) && isset($value["depends"]))
      {
        foreach($value["depends"] as $field)
        {
          // Si la variable cible n'existe pas, on la crée
          if(!isset($q[$field]))
          {
            $q[$field] = "";
          }
        }
      }

      // Variables manquantes ?
      if(!isset($q[$name]))
      {
        if(isset($value["required"]) && $value["required"] === TRUE)
        {
          $q[$name] = "";
        }
        else if(!isset($value["required"]) || $value["required"] === FALSE)
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
        if($rules[$name]["type"] === "string")
        {
          $value = empty($value) ? "" : $value;
          $value = strval($value);

          if(isset($rules[$name]["accept"]))
          {
            if(gettype($rules[$name]["accept"]) == "array")
            {
              $q[$name] = !in_array($value, $rules[$name]["accept"]) ? $rules[$name]["accept"][0] : $value;
            }
            else
            {
              $q[$name] = $value != $rules[$name]["accept"] ? $rules[$name]["accept"] : $value;
            }
          }
        }
        // Valeur de type int
        else if($rules[$name]["type"] === "integer")
        {
          $value = empty($value) ? 0 : $value;
          $value = intval($value);

          if(isset($rules[$name]["range"]))
          {
            if($value <= $rules[$name]["range"][0])
            {
              $q[$name] = strval($rules[$name]["range"][0]);
            }
            else if($value > $rules[$name]["range"][1])
            {
              $q[$name] =  strval($rules[$name]["range"][1]);
            }
            else if(($value % $rules[$name]["range"][0]) != 0)
            {
              $q[$name] =  strval($rules[$name]["range"][0]);
            }
          }
          else if(isset($rules[$name]["min"]) && ($q[$name] < $rules[$name]["min"]))
          {
            $q[$name] = strval($rules[$name]["min"]);
          }
          else if(isset($rules[$name]["max"]) && ($q[$name] > $rules[$name]["max"]))
          {
            $q[$name] = strval($rules[$name]["max"]);
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
    if(http_build_query($q) != $_SERVER["QUERY_STRING"])
    {
      // On recharge la page
      $url = self::current_url("?" . http_build_query($q));
      \core\libraries\IZI_Http::redirect($url);
    }
  }

  public static function current_url($path = "")
  {
    $url =  self::get_url();
    $url .= (gettype($path) == "array") && count($path) > 0 ? "/" . implode("/", $path) : $path;

    return self::site_url($url);
  }

  public static function set_queries($queries)
  {
    \core\libraries\IZI_Url::set_queries($queries);
  }

  public static function site_url($path = "")
  {
    $protocol = \core\libraries\IZI_Url::get_protocol();
    $host = \core\libraries\IZI_Url::get_host();
    $url = (gettype($path) == "array") && (count($path) != 0) ? implode("/", $path) : $path;

    return $protocol . "://". $host . ($path != "" ?  "/" . $path : "");
  }
}
