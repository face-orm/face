<?php

namespace Face\Sql\Query\Clause\Where;

use Face\Sql\Query\Clause\Select\Column;
use Face\Sql\Query\Clause\SqlClauseInterface;
use Face\Sql\Query\Clause\Where;
use Face\Sql\Query\FQuery;

class WhereGroup extends AbstractWhereClause
{

    /**
     * @var SqlClauseInterface
     */
    protected $subquery;

    /**
     * @var Column[]
     */
    protected $fields;

    function __construct($subquery, $fields)
    {
        $this->subquery = $subquery;
        $this->fields = $fields;
    }


    public function getSqlString(FQuery $q)
    {
        $string = "(";

        $i=0;
        foreach($this->fields as $field){
            if($i>0){
                $string .= ", ";
            }
            $string .= $field->getSqlPath();
            $i++;
        }

        $string .= ") IN " . $this->subquery->getSqlString($q);

        return $string;
    }

}
