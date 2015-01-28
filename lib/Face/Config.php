<?php

namespace Face;

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
}
