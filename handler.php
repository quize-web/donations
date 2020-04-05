<?php

require_once("./_common.php");

use app\Services\Yandex;
use app\Services\Names;


/* *** */

$data = $_POST;
if ($data):

  if (isset($data["names"]) && $data["names"]) {
    $names = new Names($data["customer-name"], $data["names"]);
    $names->createNew();
  }

  $yandex = new Yandex($data, (($names === null) ? null : $names));

  $endpoint = Yandex::ENDPOINT;
  if ((Yandex::ENDPOINT === "/callback.php") && $names) {
    $endpoint .= "?orderNumber={$yandex->getData()["orderNumber"]}";
  }

  # редиректим на Яндекс.Кассу с POST-параметрами
  if (true):
    ?>

      <html xmlns="http://www.w3.org/1999/xhtml">
      <head>

          <script type="text/javascript">
              function redirect() {
                  document.forms["redirect"].submit();
              }
          </script>

      </head>
      <body onload="redirect();">

      <form name="redirect" method="POST" action="<?= $endpoint ?>">
        <?php
        if ($yandex->getData()):
          foreach ($yandex->getData() as $name => $value):
            ?>

              <input type='hidden' name='<?= $name ?>' value='<?= $value ?>'>

          <?php
          endforeach;
        endif;
        ?>
      </form>

      </body>
      </html>

  <?php
  endif;
endif;
exit();