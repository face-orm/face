<?php

namespace Face;

/**
 * Face\DiORM is a class usable in a DI that make it easyer to use in a framework
 * @author sghzal
 */
abstract class DiORM {
    
    /**
     *
     * @var \PDO
     */
    protected $pdo;
    
    function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function getPdo() {
        return $this->pdo;
    }

    public function setPdo(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    
    /**
     * 
     * @param \Face\Sql\Query\FQuery $fQuery
     * @param \PDO $pdo
     * @return Sql\Result\ResultSet
     */
    public static function execute(Sql\Query\FQuery $fQuery){
        ORM::execute($fQuery, $this->pdo);
    }
    
}