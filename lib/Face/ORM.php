<?php

namespace Face;

use Face\Core\InstancesKeeper;
use Face\Sql\Hydrator\Generated\ArrayHydrator;
use Face\Sql\Result\ResultSet;
use Face\Util\OOPUtils;

/**
 * Face\ORM is a class that interfaces common calls of the Face API
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
        $statement = $fQuery->execute($pdo);

        if (!$statement->rowCount()) {
            return new ResultSet($fQuery->getBaseFace(), new InstancesKeeper());
        }

        $hydrator = new ArrayHydrator();

        $rs = $hydrator->hydrate($fQuery, $statement);
        return $rs;
    }

}
