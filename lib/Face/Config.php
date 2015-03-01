<?php

namespace Face;
use Face\Cache\CacheInterface;
use Face\Core\FaceLoader;
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
     * @var CacheInterface
     */
    protected $cacheAdapter;

    /**
     *
     * @var \PDO
     */
    protected $pdo;

    /**
     * @var FaceLoader
     */
    protected $faceLoader;




    /**
     * @return FaceLoader
     */
    public function getFaceLoader(){
        return $this->faceLoader;
    }

    /**
     * @param FaceLoader $faceLoader
     */
    public function setFaceLoader($faceLoader)
    {
        $this->faceLoader = $faceLoader;
    }

    /**
     * @return \PDO
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * @param \PDO $pdo
     */
    public function setPdo($pdo)
    {
        $this->pdo = $pdo;
    }





    // STATIC FOR GLOBAL CONFIG

    public static function setDefault(Config $default = null){
        self::$default = $default;
    }

    /**
     * @return Config
     */
    public static function getDefault(){
        if(self::$default) {
            return self::$default;
        }else{
            throw new \Exception("No default configuration");
        }
    }
}
