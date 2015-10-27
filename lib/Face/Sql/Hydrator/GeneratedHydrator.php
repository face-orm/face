<?php

namespace Face\Sql\Hydrator;

use Face\Cache\CacheInterface;
use Face\Cache\NoCache;
use Face\Exception;
use Face\Sql\Query\FQuery;

abstract class GeneratedHydrator extends AbstractHydrator
{
    /**
     * @var CacheInterface
     */
    protected $cache;

    protected $debugInFile = false;

    public function __construct(CacheInterface $cache = null){
        if (!$cache) {
            $this->cache = new NoCache();
        } else{
            $this->cache = $cache;
        }
    }

    public function hydrate(FQuery $FQuery, \PDOStatement $statement)
    {
//        $key = $this->getquerykey($FQuery);
//        if($this->cache->exists($key)){
//            $code = $this->cache->get($key);
//        }else{
//            $bu = microtime(true);
//            $code = $this->generatecode($FQuery);
//            $this->cache->set($key, $code);
//        }
//
//        if($this->debugInFile){
//            file_put_contents("/tmp/blabla.php", "<?php " . $code);
//            $code = include "/tmp/blabla.php";
//        }else{
//            $code = eval($code);
//        }

        $code = $this->generatecode($FQuery);
        $code = eval($code);


        if(!is_callable($code)){
            throw new Exception("Invalide code from hydrator");
        }

        return $code($statement);

    }

    abstract protected function generateCode(FQuery $FQuery);

    protected function getQueryKey(FQuery $FQuery){
        $key = $FQuery->getBaseFace()->getSqlTable();
        $joins = [];
        foreach($FQuery->getJoins() as $join){
            $joins[] = $join->getFace()->getSqlTable();
        }
        sort($joins);
        $key .= "::" . implode("::", $joins);
        return $key;
    }

}
