<?php


namespace Face\Core\FaceLoader;


use Face\Core\EntityFace;
use Face\Core\FaceLoader;
use Face\Core\FaceLoaderInterface;

/**
 * Class ArrayLoader
 *
 * A basic, non-caching loader that loads from a raw array of arrays of properties
 *
 * It's only good to use it in development
 *
 */
class ArrayLoader extends FaceLoader{

    function __construct($array)
    {
        foreach($array as $a){
            $this->addFace(new EntityFace($a));
        }

    }
}