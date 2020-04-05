<?php

if (function_exists('env') === false) {
  /**
   * @param  string  $key
   * @param  mixed   $default
   *
   * @return mixed
   */
  function env($key, $default = false)
  {
    require_once(DOCROOT . DS . "app" . DS . "Core" . DS . "Env.php");
    return app\core\Env::get($key, $default);
  }
}