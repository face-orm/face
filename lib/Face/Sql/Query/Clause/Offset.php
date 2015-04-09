<?php


namespace Face\Sql\Query\Clause;

use Face\Sql\Query\FQuery;

class Offset implements SqlClauseInterface {

    protected $offset;

    function __construct($offset)
    {
        $this->offset = $offset;
    }


    public function getSqlString(FQuery $q)
    {
        if($this->offset > 0){
            return "OFFSET " . $this->offset;
        }
        return "";
    }


}