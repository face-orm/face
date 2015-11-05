<?php

namespace Face\Sql\Hydrator;

use Face\Sql\Query\FQuery;

abstract class AbstractHydrator
{

    abstract public function hydrate(FQuery $FQuery, \PDOStatement $statement);

}
