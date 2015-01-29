<?php

namespace Face\Core;

use Face\Exception\FaceClassDoesntExistsException;
use Face\Exception\FaceDoesntExistsException;
use Face\Exception\FaceNameDoesntExistsException;

/**
 * FaceLoader
 * a pool of available faces
 */
class FaceLoader
{

    /**
     * @var EntityFace[]
     */
    protected $facesByName;

    /**
     * @var EntityFace[]
     */
    protected $facesByClass;


    /**
     * Add a face to the list of available faces
     * @param EntityFace $face
     */
    public function addFace(EntityFace $face){
        $this->facesByName[$face->getName()] = $face;
        $this->facesByClass[$face->getClass()] = $face->getName();
    }

    /**
     * @param $className
     * @return EntityFace
     * @throws FaceDoesntExistsException
     */
    public function getFaceForClass($className)
    {
        if(!isset($this->facesByClass[$className])) {
            throw new FaceClassDoesntExistsException($className);
        }
        return $this->getFaceForName($this->facesByClass[$className]);
    }

    /**
     * Checks if a face is instanciated for the given className
     * @param $className
     * @return bool
     */
    public function faceClassExists($className){
        return isset($this->facesByClass[$className]);
    }

    /**
     * @param $className
     * @return EntityFace
     * @throws FaceDoesntExistsException
     */
    public function getFaceForName($name)
    {
        if(!isset($this->facesByName[$name])) {
            throw new FaceNameDoesntExistsException($name);
        }
        return $this->facesByName[$name];
    }

    /**
     * Checks if a face is instanciated for the given entity name
     * @param $className
     * @return bool
     */
    public function faceNameExists($name){
        return isset($this->facesByName[$name]);
    }


}
