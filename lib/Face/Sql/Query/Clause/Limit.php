<?php


namespace Face\Sql\Query\Clause;


use Face\Sql\Query\QueryInterface;

class Limit implements SqlClauseInterface {

    protected $limit;

    function __construct($limit)
    {
        $this->limit = $limit;
    }


    public function getSqlString(QueryInterface $q)
    {

        if($this->limit > 0){
            return "LIMIT " . $this->limit;
        }

        return "";

    }


}
