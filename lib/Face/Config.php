<?php

namespace Face;
use Face\Core\FaceLoaderInterface;

/**
 * Allows to configure Face Adapters and functionnalities
 *
 * Possible configs are :
 *      - cache  : if available, defines how the cache is read (filesystem, memcache, redis...)
 *      - reader : query reader for hydration
 *      - pdo    : pdo object for db connexion
 * @author sghzal
 */
class Config
{

    /**
     * @var Config
     */
    protected static $default;

    /**
     *
     * @var Cache\CacheAdapter
     */
    protected $cacheAdapter;
    /**
     *
     * @var
     */
    protected $reader;
    /**
     *
     * @var \PDO
     */
    protected $pdo;

    /**
     * @var FaceLoaderInterface
     */
    protected $faceLoader;


    public static function setDefault(Config $default){
        self::$default = $default;
    }

    /**
     * @return Config
     */
    public static function getDefault(){
        if(self::$default) {
            return self::$default;
        }else{
            throw new \Exception("No configuration");
        }
    }

    /**
     * @return FaceLoaderInterface
     */
    public function getFaceLoader(){
        return $this->faceLoader;
    }

    /**
     * @param FaceLoaderInterface $faceLoader
     */
    public function setFaceLoader($faceLoader)
    {
        $this->faceLoader = $faceLoader;
    }



}
