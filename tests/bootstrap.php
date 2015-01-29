<?php
include_once( __DIR__ . '/../vendor/autoload.php');
// Register the directory to your include files

$config = new \Face\Config();

$facesArray = include( __DIR__ . "/res/model-definitions/array.php");

//$config->setFaceLoader(new \Face\Core\FaceLoader\ArrayLoader($facesArray));
$config->setFaceLoader(new \Face\Core\FaceLoader\FileReader\PhpArrayReader( __DIR__ . "/res/model-definitions/arrayList" ));

$config::setDefault($config);