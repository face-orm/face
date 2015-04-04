<?php

namespace Face\Sql\Query\Clause;


use Face\Sql\Query\FQuery;

interface SqlClauseInterface {

    public function getSqlString(FQuery $q);

}