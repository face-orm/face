<?php

namespace Face\Sql\Query\Clause;


use Face\Sql\Query\Clause\Select\Column;
use Face\Sql\Query\QueryInterface;

class GroupBy implements SqlClauseInterface{

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
        $string = "GROUP BY ";

        $i=0;
        foreach($this->columns as $column){
            if($i>0){
                $string .= ", ";
            }
            $string .= $column->getSqlPath();
            $i++;
        }

        return $string;

    }


}
