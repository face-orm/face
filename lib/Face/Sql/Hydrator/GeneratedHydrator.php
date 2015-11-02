<?php

namespace Face\Sql\Hydrator;

use Face\Cache\CacheInterface;
use Face\Cache\NoCache;
use Face\Exception;
use Face\Sql\Query\FQuery;
use Face\Sql\Result\ResultSet;

abstract class GeneratedHydrator extends AbstractHydrator
{
    /**
     * @var CacheInterface
     */
    protected $cache;

    protected $debugInFile = true;

    public function __construct(CacheInterface $cache = null){
        if (!$cache) {
            $this->cache = new NoCache();
        } else{
            $this->cache = $cache;
        }
    }

    /**
     * @param FQuery $FQuery
     * @param \PDOStatement $statement
     * @return ResultSet
     * @throws Exception
     */
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


        if($this->debugInFile){
            $code = $this->generatecode($FQuery);
            $filePath = sys_get_temp_dir() . "/face_QH_" . $this->getQueryKey($FQuery) . ".php" ;
            file_put_contents($filePath,  "<?php" . $code);
            $code = include $filePath;
        }else{
            $code = $this->generatecode($FQuery);
            $code = eval($code);
        }



        if(!is_callable($code)){
            throw new Exception("Invalide code from hydrator");
        }

        $data = $code($statement);

        $resultset = new ResultSet($FQuery->getBaseFace());
        $resultset->setInstances($data);

        return $resultset;

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
