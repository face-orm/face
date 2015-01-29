<?php
include_once( __DIR__ . '/../vendor/autoload.php');
// Register the directory to your include files


$config = new \Face\Config();

$facesArray = include( __DIR__ . "/faces-definition.php");

$config->setFaceLoader(new \Face\Core\FaceLoader\ArrayLoader($facesArray));

$config::setDefault($config);