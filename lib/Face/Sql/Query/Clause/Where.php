<?php

namespace Face\Sql\Query\Clause;

use Face\Sql\Query\FQuery;

abstract class Where {

    abstract public function getSqlString(FQuery $q);

}