<?php

namespace Face\Sql\Query\Clause;

use Face\Sql\Query\FQuery;

interface WhereInterface
{

    public function getSqlString(FQuery $q);
}
