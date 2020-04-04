<?php

require_once (DOCROOT . DS . "app/Core/Loader.php");
\app\Core\Loader::init();

/* *** */

use XMLWriter;
use app\Core\XML;
use app\Services\Names;

$XMLwriter = new XMLWriter();
$XML = new XML();
$names = new Names();


echo '<pre>';
print_r("done.");
echo '</pre>';
exit();