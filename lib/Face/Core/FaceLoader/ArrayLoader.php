<?php


namespace Face\Core\FaceLoader;


use Face\Core\EntityFace;
use Face\Core\FaceLoaderInterface;

class ArrayLoader implements FaceLoaderInterface{

    protected $array;

    /**
     * @var EntityFace[]
     */
    protected $facesByName;

    /**
     * @var EntityFace[]
     */
    protected $facesByClass;

    function __construct($array)
    {
        $this->array = $array;

        foreach($array as $a){
            $this->facesByName[$a["name"]] = new EntityFace($a);
            $this->facesByClass[$a["class"]] = $this->facesByName[$a["name"]];
        }

    }

    public function getFaceForClass($className)
    {
        return $this->facesByClass[$className];
    }

    public function getFaceForName($name)
    {
        return $this->facesByClass[$name];
    }


}