<?php

namespace app\Core;


class Loader
{


  /**
   * @return void
   */
  public static function init()
  {
    spl_autoload_register([get_called_class(), "register"], true);
  }


  /**
   * @param  string  $class
   * @return void
   */
  private static function register($class)
  {
    $path = explode("\\", $class);
    $fullPath = (DOCROOT . DS . implode(DS, $path) . ".php");
    include($fullPath);
  }


}