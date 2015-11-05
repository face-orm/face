<?php

namespace Face\Sql\Query\Clause;


use Face\Core\EntityFace;
use Face\Exception\BadParameterException;
use Face\Sql\Query\QueryInterface;

class From implements SqlClauseInterface{

    /**
     * @var string|SqlClauseInterface|EntityFace
     */
    protected $fromItem;

    function __construct($what)
    {
        $this->fromItem = $what;
    }

    public function getSqlString(QueryInterface $q)
    {
        $table = null;
        if(is_string($this->fromItem)){
            $table = $this->fromItem;
        }else if(is_object($this->fromItem)){
            if($this->fromItem instanceof EntityFace){
                $table = $this->fromItem->getSqlTable(true);
            }else if($this->fromItem instanceof SqlClauseInterface){
                $table = $this->fromItem->getSqlString($q);
            }
        }

        if(null === $table){
            throw new BadParameterException("Bad parameter for From clause");
        }

        return "FROM " . $table . " AS `this`";
    }


}
