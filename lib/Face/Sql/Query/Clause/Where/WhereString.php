<?php

namespace Face\Sql\Query\Clause\Where;

use Face\ContextAwareInterface;
use Face\Sql\Query\Clause\SqlClauseInterface;
use Face\Sql\Query\Clause\Where;
use Face\Sql\Query\QueryInterface;
use Face\Traits\ContextAwareTrait;

class WhereString extends AbstractWhereClause implements SqlClauseInterface, ContextAwareInterface
{
    use ContextAwareTrait;

    protected $string;

    function __construct($string)
    {
        $this->string = $string;
    }

    public function getSqlString(QueryInterface $q)
    {
        return $q->parseColumnNames($this->string, $this);
    }
}
