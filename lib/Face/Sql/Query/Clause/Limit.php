<?php


namespace Face\Sql\Query\Clause;

use Face\Sql\Query\FQuery;

class Limit implements SqlClauseInterface {

    protected $limit;

    function __construct($limit)
    {
        $this->limit = $limit;
    }


    public function getSqlString(FQuery $q)
    {

        if($this->limit > 0){
            return "LIMIT " . $this->limit;
        }

        return "";

    }


}