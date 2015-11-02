<?php
/**
 * Created by PhpStorm.
 * User: bob
 * Date: 11/2/15
 * Time: 11:37 PM
 */

namespace Face\Sql\Query;


use Face\Sql\Query\SelectBuilder\JoinQueryFace;

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
     * array of columns to be selected with their alias in this form : $array["alias"] = "real.path"
     * @return array
     */
    public function getSelectedColumns()
    {
        $finalColumns = parent::getSelectedColumns();
//
//        foreach($this->joins as $join){
//            $finalColumns = array_merge($finalColumns,$join->getColumnsReal());
//        }


        return $finalColumns;
    }

    /**
     * @inheritdoc
     */
    public function getAvailableQueryFaces()
    {
        return array_merge(parent::getAvailableQueryFaces() + $this->getJoins());
    }

}
