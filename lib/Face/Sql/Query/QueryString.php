<?php
/**
 * @author Soufiane GHZAL
 * @copyright Laemons
 * @license MIT
 */

namespace Face\Sql\Query;

use Face\Core\EntityFace;

class QueryString extends FQuery
{

    protected $sqlString;

    private $whereInCount=0;

    function __construct(EntityFace $baseFace, $sqlString, $joins = [], $selectedColumns = [])
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

    /**
     * binds an array of value.
     *
     * Intended to be used in such a case :
     * <pre>
     *  WHERE something IN (::in::)
     * </pre>
     *
     * then ->bindIn("::in::",$arrayOfValues)
     *
     * @param $token string the bound token
     * @param $array array list of values to bind
     * @return $this QueryString
     */
    public function bindIn($token, $array)
    {
        $bindString = "";
        foreach ($array as $value) {
            $bindString.=',:fautoIn'.++$this->whereInCount;
            $this->bindValue(':fautoIn'.$this->whereInCount, $value);
        }

        // TODO saffer replace
        $this->sqlString = str_replace($token, ltrim($bindString, ","), $this->sqlString);

        return $this;

    }
}
