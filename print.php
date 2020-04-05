<?php

require_once("./_common.php");

use app\Services\Names;


$allNames = Names::getNames();
//echo '<pre>';
//print_r($allNames);
//echo '</pre>';
//exit();

foreach ($allNames as $type => $names):
  ?>

    ...

<?php
endforeach;