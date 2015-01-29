<?php

namespace Face\Core\FaceLoader;


use Face\Cache\CacheInterface;
use Face\Core\EntityFace;
use Face\Core\FaceLoader;
use Face\Core\FaceLoaderInterface;
use Face\Exception\FaceClassDoesntExistsException;

abstract class CachableLoader extends  FaceLoader {

    private $loaded = true;

    /**
     * @var CacheInterface
     */
    private $cache;


    public function setCache(CacheInterface $cache){
        $this->cache = $cache;
        $this->loaded = false;
    }

    /**
     * @inheritdoc
     */
    public function getFaceForClass($className)
    {
        if($this->faceClassExists($className)){
            return parent::getFaceForClass($className);
        }else{
            // todo unserialize ?
            $face = $this->cache->get("class_" . $className);
            if(!$face){
                if(!$this->loaded){
                    $this->loadAndCacheFaces();
                }
            }else{
                $this->addFace($face);
                return $face;
            }
        }
        return parent::getFaceForClass($className);
    }

    /**
     * @inheritdoc
     */
    public function getFaceForName($name)
    {
        if($this->faceNameExists($name)){
            return parent::getFaceForName($name);
        }else{
            // todo unserialize ?
            $face = $this->cache->get("name_" . $name);
            if(!$face){
                if(!$this->loaded){
                    $this->loadAndCacheFaces();
                }
            }else{
                $this->addFace($face);
                return $face;
            }
        }
        return parent::getFaceForName($name);
    }

    /**
     * Load all faces and cache them
     */
    protected function loadAndCacheFaces(){
        $faces = $this->_loadFaces();
        foreach($faces as $face){
            $this->cacheFace($face);
            $this->addFace($face);
        }
    }

    /**
     * register a face in the cache
     * @param EntityFace $faces
     */
    protected function cacheFace(EntityFace $faces){
        foreach($faces as $face){
            // TODO : serialize it ?
            $this->cache->set("name_".$face->getName(),$face);
            $this->cache->set("class_".$face->getClass(),$face);
        }
    }

    /**
     * This method is called to parse ALL the faces and cache them
     *
     * It will never be called twice
     * because it is assumed that one run will return all the available faces
     *
     * @return EntityFace[]
     */
    abstract protected function _loadFaces();

}