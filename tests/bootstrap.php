<?php
include_once( __DIR__ . '/../vendor/autoload.php');
// Register the directory to your include files

$config = new \Face\Config();

$facesArray = include( __DIR__ . "/res/model-definitions/array.php");


$redis = new Redis();
$redis->connect("127.0.0.1");
//$redis->flushAll();
$cache = new \Face\Cache\RedisCache($redis);

//$config->setFaceLoader(new \Face\Core\FaceLoader\ArrayLoader($facesArray));
$cacheableLoader = new \Face\Core\FaceLoader\FileReader\PhpArrayReader( __DIR__ . "/res/model-definitions/arrayList" );
$cacheableLoader->setCache($cache);
$config->setFaceLoader($cacheableLoader);


$config::setDefault($config);