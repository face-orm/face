<?php
/**
 * @author Soufiane GHZAL
 * @copyright Laemons
 * @license MIT
 */

namespace Face\Sql\Query;

use Face\Core\EntityFace;
use Face\Sql\Query\SelectBuilder\JoinQueryFace;
use Face\Sql\Query\SelectBuilder\QueryFace;

class QueryString extends FQuery
{

    protected $sqlString;

    private $whereInCount=0;

    /**
     * @param EntityFace $baseFace
     * @param $sqlString
     * @param JoinQueryFace[] $joins
     */
    function __construct(EntityFace $baseFace, $sqlString)
    {
        parent::__construct($baseFace);
        $this->sqlString = $sqlString;
    }

    /**
     * @param $path
     * @param null $columns
     * @return JoinQueryFace
     * @throws \Exception
     * @throws \Face\Exception\RootFaceReachedException
     */
    public function setJoin($path, $columns = null){

        $sqlPath = $this->_doFQLTableName($path, ".");

        $this->joins[$sqlPath] = new JoinQueryFace($sqlPath, $this->baseFace->getElement($path)->getFace(), $this);
        if($columns){
            $this->joins[$sqlPath]->setColumns($columns);
        }
        return $this->joins[$sqlPath];
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
            $bindString .= ',:fautoIn' . ++$this->whereInCount;
            $this->bindValue(':fautoIn' . $this->whereInCount, $value);
        }

        // TODO saffer replace
        $this->sqlString = str_replace($token, ltrim($bindString, ","), $this->sqlString);

        return $this;

    }
}
