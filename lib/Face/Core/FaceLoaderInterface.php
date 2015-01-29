<?php

namespace Face\Core;

use Face\Exception\FaceDoesntExistsException;
use Face\Util\OOPUtils;

/**
 * This is an internal core class, it should be used only internally by the library
 */
interface FaceLoaderInterface
{


    /**
     * @param $className
     * @return EntityFace
     * @throws FaceDoesntExistsException
     */
    public function getFaceForClass($className);

    /**
     * @param $className
     * @return EntityFace
     * @throws FaceDoesntExistsException
     */
    public function getFaceForName($faceName);

}
