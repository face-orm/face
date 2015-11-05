<?php

namespace Face\Sql\Query\Clause;


use Face\Sql\Query\Clause\Select\Column;
use Face\Sql\Query\QueryInterface;

class Select implements SqlClauseInterface {

    /**
     * @var Column[]
     */
    protected $columns;

    function __construct($columns)
    {
        $this->columns = $columns;
    }


    public function getSqlString(QueryInterface $q)
    {
        $sql="SELECT ";


        $selectFields = [];

        foreach ($this->columns as $column) {
            $selectFields[] = $column->getSqlString($q);
        }
        $sql .= implode(", ", $selectFields);


        return $sql;
    }


}
