<?php

namespace Face\Sql\Query\Clause\Select\Column;

use Face\Core\EntityFaceElement;
use Face\Sql\Query\Clause\Select\Column;
use Face\Sql\Query\FQuery;
use Face\Sql\Query\QueryInterface;

class ExpressionColumn extends Column
{
    protected $expression;
    protected $hydrationAlias;

    function __construct($expression, $hydrationAlias = null)
    {
        $this->expression = $expression;
        $this->hydrationAlias = $hydrationAlias;
        $this->setQueryAlias($hydrationAlias);
    }

    public function getExpression(){
        return $this->expression;
    }

    public function isHydratable(){
        return null !== $this->hydrationAlias;
    }

    public function getHydrationAlias(){
        return $this->hydrationAlias;
    }

    public function getSqlStatement(QueryInterface $queryInterface)
    {

        $expression = $queryInterface->parseColumnNames($this->expression);

        return $expression;
    }


}
