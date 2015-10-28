<?php

namespace Face;

use Face\Core\InstancesKeeper;
use Face\Sql\Hydrator\Generated\ArrayHydrator;
use Face\Sql\Query\FQuery;
use Face\Sql\Query\SelectBuilder;
use Face\Sql\Query\SimpleDelete;
use Face\Sql\Query\SimpleInsert;
use Face\Sql\Query\SimpleUpdate;
use Face\Sql\Result\DeleteResult;
use Face\Sql\Result\InsertResult;
use Face\Sql\Result\ResultSet;
use Face\Sql\Result\UpdateResult;
use Face\DiORM\SelectBuilder as DiSelectBuilder;

/**
 * Face\DiORM is a class usable in a DI that make it easyer to use in a framework
 * @author sghzal
 */
class DiORM
{

    /**
     *
     * @var Config
     */
    protected $config;

    /**
     * @var InstancesKeeper
     */
    protected $instancesKeeper;

    /**
     * @param Config $config config instance
     * @param InstancesKeeper $instancesKeeper instance keeper to manage instances
     */
    function __construct(Config $config, InstancesKeeper $instancesKeeper = null)
    {
        $this->config = $config;
        $this->instancesKeeper= null !== $instancesKeeper ? $instancesKeeper : new InstancesKeeper();
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param Config $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @param $name
     * @return Core\EntityFace
     * @throws Exception\FaceNameDoesntExistsException
     */
    public function getFace($name){
        return $this->config->getFaceLoader()->getFaceForName($name);
    }

    /**
     * @param $name
     * @return DiSelectBuilder
     */
    public function selectBuilder($name){
        return new DiSelectBuilder($this->getFace($name), $this);
    }

    /**
     * Execute a select query, parse the result and returns a resultSet
     * @param \Face\Sql\Query\FQuery $fQuery
     * @return Sql\Result\ResultSet
     */
    public function select(Sql\Query\FQuery $fQuery, $useGlobalInstanceKeeper = false)
    {
        $statement=$fQuery->execute($this->config->getPdo());

        if($useGlobalInstanceKeeper){
            $instanceKeeper = $this->instancesKeeper;
        }else{
            $instanceKeeper = new InstancesKeeper();
        }

        if (!$statement->rowCount()) {
            return new ResultSet($fQuery->getBaseFace(), $instanceKeeper);
        }

        $hydrator = new ArrayHydrator();
        $rs = $hydrator->hydrate($fQuery, $statement);
        return $rs;
    }

    /**
     * @param FQuery $fQuery
     * @return InsertResult
     * @throws \Exception
     */
    public function insert(Sql\Query\FQuery $fQuery){
        $pdo = $this->config->getPdo();
        $statement = $fQuery->execute($pdo);
        return new InsertResult($statement,$pdo->lastInsertId());
    }

    /**
     * performs a SimpleInsert query for the given entity
     * @param $entity
     * @return InsertResult
     */
    public function simpleInsert($entity){
        $simpleInsert = new SimpleInsert($entity,$this->getConfig());
        return $this->insert($simpleInsert);
    }

    /**
     * @param FQuery $fQuery
     * @return UpdateResult
     * @throws \Exception
     */
    public function update(FQuery $fQuery){
        $pdo = $this->config->getPdo();
        $statement = $fQuery->execute($pdo);
        return new UpdateResult($statement);
    }

    /**
     * performs a SimpleUpdate query for the given entity
     * @param $entity
     * @return UpdateResult
     */
    public function simpleUpdate($entity){
        $simpleUpdate = new SimpleUpdate($entity,$this->getConfig());
        return $this->update($simpleUpdate);
    }


    /**
     * @param FQuery $fQuery
     * @return DeleteResult
     * @throws \Exception
     */
    public function delete(FQuery $fQuery){

        $pdo = $this->config->getPdo();

        $statement = $fQuery->execute($pdo);

        return new DeleteResult($statement);

    }

    /**
     * performs a SimpleDelete query for the given entity
     * @param $entity
     * @return DeleteResult
     */
    public function simpleDelete($entity){
        $simpleDelete = new SimpleDelete($entity,$this->getConfig());
        return $this->delete($simpleDelete);
    }
}
