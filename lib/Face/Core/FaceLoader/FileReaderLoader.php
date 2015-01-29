<?php

namespace Face\Core\FaceLoader;


use Face\Core\EntityFace;
use Face\Core\FaceLoader;

/**
 * Class FileReaderLoader
 *
 * Foreach files in the list loads an Entity.
 * It allows to keep a clean file structure were each file contains 1 definition
 *
 */
abstract class FileReaderLoader extends CachableLoader {

    protected $directory;

    protected $filePattern;

    /**
     * should get files recursively
     * @var boolean
     */
    protected $recursive;

    function __construct($directory,$pattern = null)
    {
        $this->directory = $directory;
        $this->filePattern = $pattern;
        $this->recursive = true;
    }

    /**
     * @return boolean
     */
    public function isRecursive()
    {
        return $this->recursive;
    }

    /**
     * @param boolean $recursive
     */
    public function setRecursive($recursive)
    {
        $this->recursive = $recursive;
    }




    /**
     * @inheritdoc
     */
    protected function _loadFaces()
    {

        $faces = array();

        if($this->isRecursive()){
            $dirIterator = new \RecursiveDirectoryIterator($this->directory);
        }else{
            $dirIterator = new \DirectoryIterator($this->directory);
        }

        if($this->filePattern){
            $dirIterator = new \RegexIterator($dirIterator,$this->filePattern);
        }

        foreach($dirIterator as $file){
            if($file->isFile()){

                $fileName = $file->getPathname();
                $faces[] = $this->readFile($fileName);


            }
        }

        return $faces;
    }

    /**
     * transform a file
     * @param $fileName
     * @return EntityFace
     */
    protected abstract function readFile($fileName);


}