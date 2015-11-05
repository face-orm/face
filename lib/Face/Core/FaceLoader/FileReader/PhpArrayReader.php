<?php


namespace Face\Core\FaceLoader\FileReader;


use Face\Core\EntityFace;
use Face\Core\FaceFactory;
use Face\Core\FaceLoader\FileReaderLoader;

class PhpArrayReader extends  FileReaderLoader{
    /**
     * transform a file
     * @param $fileName
     * @return EntityFace
     */
    protected function readFile($fileName)
    {
        // todo : handle errors
        return FaceFactory::buildFace(require $fileName, $this);
    }


}
