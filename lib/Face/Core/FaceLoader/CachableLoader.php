<?php

namespace Face\Core\FaceLoader;


use Face\Cache\CacheInterface;
use Face\Cache\NoCache;
use Face\Core\EntityFace;
use Face\Core\FaceLoader;
use Face\Core\FaceLoaderInterface;
use Face\Exception\FaceClassDoesntExistsException;

abstract class CachableLoader extends  FaceLoader {

    private $loaded = false;

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
        $face = null;

        if($this->faceClassExists($className)){
            return parent::getFaceForClass($className);
        }else{
            // todo unserialize ?
            $face = $this->getCache()->get("class_" . $className);
            if(!$face){
                if(!$this->loaded){
                    $this->loadAndCacheFaces();
                }
            }else{
                //todo : improve
                $face = unserialize($face);
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

            $face = $this->getCache()->get("name_" . $name);

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

    public function getCache(){
        if(null === $this->cache){
            $this->cache = NoCache::singleInstance();
        }
        return $this->cache;
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
    protected function cacheFace(EntityFace $face){
        // todo : improve
        $serialized = serialize($face);
        $this->getCache()->set("name_".$face->getName(),$serialized);
        $this->getCache()->set("class_".$face->getClass(),$serialized);
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
