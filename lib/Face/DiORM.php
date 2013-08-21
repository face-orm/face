<?php

namespace Face;
use Face\Core\InstancesKeeper;

/**
 * Face\DiORM is a class usable in a DI that make it easyer to use in a framework
 * @author sghzal
 */
class DiORM {
    
    /**
     *
     * @var \PDO
     */
    protected $pdo;

    /**
     * @var InstancesKeeper
     */
    protected $ik;

    /**
     * @param \PDO $pdo pdo instance for db connections
     * @param InstancesKeeper $instancesKeeper instance keeper to manage instances
     */
    function __construct(\PDO $pdo,InstancesKeeper $instancesKeeper=null) {
        $this->pdo = $pdo;
        $this->ik=$instancesKeeper;
    }
    
    public function getPdo() {
        return $this->pdo;
    }

    public function setPdo(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    
    /**
     * Execute a select query, parse the result and returns a resultSet
     * @param \Face\Sql\Query\FQuery $fQuery
     * @return Sql\Result\ResultSet
     */
    public function execute(Sql\Query\FQuery $fQuery){
        $j=$fQuery->execute($this->pdo);

        $reader=new \Face\Sql\Reader\QueryArrayReader($fQuery,$this->ik);

        $rs=$reader->read($j);

        return $rs;
    }
    
}