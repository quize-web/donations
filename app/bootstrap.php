<?php

require_once("." . DS . "app" . DS . "functions.php");

$subFolder = env('SUBFOLDER', null);
$subFolder = ($subFolder ? (DS . "{$subFolder}") : "");

define("DOCROOT", ($_SERVER["DOCUMENT_ROOT"] . $subFolder));

require_once(DOCROOT . DS . "app" . DS . "Core" . DS . "Loader.php");

\app\Core\Loader::init();