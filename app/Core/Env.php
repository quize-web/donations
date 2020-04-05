<?php

namespace app\core;


class Env
{


  /**
   * @var null|array $data
   */
  private static $data = null;


  /**
   * @return void
   */
  public static function read(): void
  {
    $envFileFullPath = (DOCROOT . DS . ".env");
    if (file_exists($envFileFullPath) && is_null(self::$data)) {
      self::$data = [];
      foreach (explode("\n", file_get_contents($envFileFullPath)) as $row) {
        $row = trim($row);

        if ($row == '') continue;
        if (substr($row, 0, 1) == "#") continue;

        [$key, $value] = explode("=", $row, 2);

        if ($value === "") continue;
        if (strtolower($value) === "true") $value = true;
        if (strtolower($value) === "false") $value = false;
        if (strtolower($value) == "null") $value = null;

        self::$data[$key] = $value;
      }
    }
  }


  /**
   * @param  string  $key
   * @param  mixed   $default
   *
   * @return mixed
   */
  public static function get(string $key, $default = false)
  {
    if (is_null(self::$data)) self::read();

    if (is_array(self::$data) && array_key_exists($key, self::$data)) {
      return self::$data[$key];
    }

    return $default;
  }


  /**
   * @param  string  $key
   *
   * @return mixed
   */
  function __get(string $key)
  {
    return self::get($key);
  }


  /**
   * @param  string  $key
   * @param  string  $value
   */
  function __set(string $key, string $value): void
  {
    # ...
  }


}