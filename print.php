<?php

header("Content-Type: text/html; charset=UTF-8");
require_once("." . DIRECTORY_SEPARATOR . "_common.php");

use app\Services\Names;


if (isset($_POST["to-archive"]) && $_POST["to-archive"]) {
  # нажали на кнопку "Удалить" - переносим файл с именам в архив
  Names::moveMainFileToArchive($_POST["to-archive"]);
}

if (isset($_GET["auth"]) && ($_GET["auth"] === Names::AUTH_HASH)):
  # хеш-пароль совпал - показвыаем имена
  $allNames = Names::getNames();
  ?>

    <!DOCTYPE HTML>
    <html lang="ru">
    <head>
        <meta charset="utf-8"/>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Имена-<?= date("d_m_Y__H_i_s") ?></title>

        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link href="css/font.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <link rel="stylesheet" type="text/css" href="css/printable.css" media="print">

        <script src="js/jquery-3.4.1.slim.min.js"></script>
        <script src="js/bootstrap.min.js"></script>

    </head>
    <body class="printable">

    <div class="container">

        <div class="printable__button">
            <button class="btn btn-primary" onclick="window.print();return false;">Печать</button>
        </div>

      <?php if ($allNames): ?>
          <div class="row names">
            <?php foreach ($allNames as $type => $names): ?>
                <div class="col-sm">

                    <div class="names__title">
                        <span>↓ </span>
                        <h2>
                          <?php $id = "names__{$type}"; ?>
                            <a href="#<?= $id ?>" aria-controls="<?= $id ?>" data-toggle="collapse" role="button"
                               aria-expanded="false">
                                <span><?= Names::$TYPES[$type]["title"] ?>:</span>
                            </a>
                        </h2>
                      <?php if (Names::expires($type)): ?>
                          <p class="names__title-addition">(<i>Обновляется автоматически</i>)</p>
                      <?php else: ?>
                          <form method="POST" class="names__title-addition">
                              <input type="hidden" name="to-archive" value="<?= $type ?>">
                              <button class="btn btn-primary">Удалить</button>
                          </form>
                      <?php endif; ?>
                    </div>

                  <?php if ($names): ?>
                      <ol class="names__list collapse show" id="<?= $id ?>">
                        <?php foreach ($names as $name): ?>
                          <?php if (is_array($name)): ?>
                                <li>
                                    <span><?= $name["value"] ?></span>
                                    <span class="names__list-item-description"> (до <?= Names::getEndDate($name["created_at"], $type, true) ?>)</span>
                                </li>
                          <?php else: ?>
                                <li>
                                    <span><?= $name ?></span>
                                </li>
                          <?php endif; ?>
                        <?php endforeach; ?>
                      </ol>
                  <?php endif; ?>

                </div>
            <?php endforeach; ?>
          </div>
      <?php endif; ?>
    </div>

    </body>
    </html>
<?php endif; ?>
