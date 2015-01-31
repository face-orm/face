<?php

namespace Face;

use Face\Core\InstancesKeeper;
use Face\Sql\Result\ResultSet;
use Face\Util\OOPUtils;

/**
 * Face\ORM is a class that interfaces comon calls of the Face API
 *
 * This abstract is made to be call everywhere in the application.
 * An alternative for dependency injection lives at Face\DiOrm
 *
 * @author sghzal
 */
abstract class ORM
{

    /**
     *
     * @param \Face\Sql\Query\FQuery $fQuery
     * @param \PDO $pdo
     * @return Sql\Result\ResultSet
     */
    public static function execute(Sql\Query\FQuery $fQuery, \PDO $pdo)
    {
        $j=$fQuery->execute($pdo);

        if (!$j->rowCount()) {
            return new ResultSet($fQuery->getBaseFace(), new InstancesKeeper());
        }

        $reader=new \Face\Sql\Reader\QueryArrayReader($fQuery);

        $rs=$reader->read($j);
        
        return $rs;
    }


    public static function executeDebug(Sql\Query\FQuery $fQuery, \PDO $pdo, &$repport){
        $beginTime = microtime(true);

        $string = $fQuery->getSqlString();
        $stringTime = microtime(true);

        $stmt = $pdo->prepare($string);
        $values = $fQuery->getBoundValues();
        foreach ($values as $name => $bind) {
            $stmt->bindValue($name, $bind[0], $bind[1]);
        }
        $prepareSttTime = microtime(true);

        $r = $stmt->execute();
        $executionTime =microtime(true);

        $reader=new \Face\Sql\Reader\QueryArrayReader($fQuery);
        $rs=$reader->read($stmt);
        $readTime = microtime(true);


        $repport = array(

            "queryParsing"   => ($stringTime - $beginTime) * 1000,
            "pdoStatementBuilding" => ($prepareSttTime -$stringTime) * 1000,
            "queryExecution" => ($executionTime - $prepareSttTime) * 1000,
            "hydrationTime"  => ($readTime - $executionTime) * 1000
        );

        return $rs;

    }
}
