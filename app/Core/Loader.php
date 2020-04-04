<?php

namespace app\Core;


class Loader
{


  /**
   * @return void
   */
  public static function init(): void
  {
    spl_autoload_register([Loader::class, "register"], true);
  }


  /**
   * @param  string  $class
   * @return void
   */
  private static function register(string $class): void
  {
    $path = explode("\\", $class);
    $fullPath = (DOCROOT . DS . implode(DS, $path) . ".php");
    include($fullPath);
  }


}