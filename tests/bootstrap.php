<?php


$path = './vendor/';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

include_once('./vendor/autoload.php');
// Register the directory to your include files

ladybug_set_format("console");