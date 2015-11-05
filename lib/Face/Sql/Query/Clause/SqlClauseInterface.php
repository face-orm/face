<?php

namespace Face\Sql\Query\Clause;


use Face\Sql\Query\QueryInterface;

interface SqlClauseInterface {

    public function getSqlString(QueryInterface $q);

}
