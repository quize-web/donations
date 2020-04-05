<?php

require_once("./_common.php");

use app\Services\Names;
use app\Services\Yandex;

/* *** */

$data = $_GET;

if (false && Yandex::ENDPOINT === "/callback.php") { # отладка

  $data = $_POST;
  echo '<pre>';
  print_r($data);
  echo '</pre>';
  exit();

} elseif (isset($data['orderNumber']) && $data['orderNumber']) { # production

  $names = new Names($data['orderNumber']);
  $names->markAsPayed();

}

header("Location: /");
exit();