<?php
/**
 * @author Soufiane GHZAL
 * @copyright Laemons
 * @license MIT
 */

namespace Face\Sql\Query;


use Face\Core\EntityFace;

class QueryString extends FQuery{

    protected $sqlString;

    function __construct(EntityFace $baseFace, $sqlString, $joins=[], $selectedColumns=[])
    {
        parent::__construct($baseFace);
        $this->sqlString = $sqlString;
        $this->joins = $joins;
        $this->selectedColumns=$selectedColumns;
    }

    public function getSqlString()
    {
        return $this->sqlString;
    }

}