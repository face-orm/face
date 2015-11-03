<?php
/**
 * Created by PhpStorm.
 * User: bob
 * Date: 11/2/15
 * Time: 11:37 PM
 */

namespace Face\Sql\Query;


use Face\Sql\Query\SelectBuilder\JoinQueryFace;
use Face\Sql\Query\SelectBuilder\QueryFace;

abstract class JoinableQuery extends FQuery
{

    /**
     * list of face joined to the query
     * @var JoinQueryFace[]
     */
    private $joins = [];

    /**
     * @return SelectBuilder\JoinQueryFace[]
     */
    public function getJoins()
    {
        return $this->joins;
    }

    public function addJoin(JoinQueryFace $queryFace){
        $this->joins[$queryFace->getPath()] = $queryFace;
    }

    /**
     * check is a face is joined to the query. This method is not aware of the current context
     * @param string $path the face path to check
     * @return bool
     */
    public function isJoined($path)
    {
        return isset($this->joins[$this->_doFQLTableName($path, ".")]);
    }

    /**
     * @return QueryFace[]
     */
    public function getAvailableQueryFaces()
    {
        return parent::getAvailableQueryFaces() + $this->getJoins();
    }

}
