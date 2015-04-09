<?php
/**
 * Created by PhpStorm.
 * User: bob
 * Date: 4/8/15
 * Time: 1:33 PM
 */

namespace Face\Sql\Query\Clause;


use Face\Sql\Query\Clause\Select\Column;
use Face\Sql\Query\FQuery;

class Select implements SqlClauseInterface {

    /**
     * @var Column[]
     */
    protected $columns;

    function __construct($columns)
    {
        $this->columns = $columns;
    }


    public function getSqlString(FQuery $q)
    {
        $selectFields = [];

        foreach ($this->columns as $column) {
            $selectFields[] = $column->getSqlString($q);
        }


        $sql="SELECT ";
        $sql.=implode(", ", $selectFields);

        return $sql;
    }


}