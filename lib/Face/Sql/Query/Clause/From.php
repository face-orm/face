<?php

namespace Face\Sql\Query\Clause;


use Face\Core\EntityFace;
use Face\Sql\Query\FQuery;

class From implements SqlClauseInterface{

    /**
     * @var EntityFace
     */
    protected $face;

    function __construct(EntityFace $face)
    {
        $this->face = $face;
    }

    public function getSqlString(FQuery $q)
    {
        $table = $this->face->getSqlTable(true);
        return "FROM " . $table . " AS `this`";
    }


}