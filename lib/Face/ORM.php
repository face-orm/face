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

}
