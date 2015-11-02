<?php


namespace Face\Sql\Query\Clause;



use Face\Sql\Query\QueryInterface;

class Offset implements SqlClauseInterface {

    protected $offset;

    function __construct($offset)
    {
        $this->offset = $offset;
    }


    public function getSqlString(QueryInterface $q)
    {
        if($this->offset > 0){
            return "OFFSET " . $this->offset;
        }
        return "";
    }


}
