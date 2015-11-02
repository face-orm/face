<?php
/**
 * Created by PhpStorm.
 * User: bob
 * Date: 11/2/15
 * Time: 10:49 PM
 */

namespace Face\Sql\Query;


use Face\Sql\Query\Clause\OrderBy\Field;
use Face\Sql\Query\Clause\Where\AbstractWhereClause;
use Face\Sql\Query\Clause\Where\WhereGroup;
use Face\Sql\Query\SelectBuilder\JoinQueryFace;
use Face\Sql\Query\SelectBuilder\LimitOnSubQueryCompiler;
use Face\Sql\Query\SelectBuilder\QueryFace;
use Face\Sql\Query\SelectBuilder\StandardCompiler;

class SelectQuery extends JoinableQuery implements SelectInterface
{

    protected $limit;
    protected $offset;

    /**
     * used by whereINRelation() to add join to the final query without parsing them
     * @var JoinQueryFace[]
     */
    protected $softThroughJoin = [];

    /**
     *
     * @var WhereGroup
     */
    protected $where;


    /**
     * @var Field[]
     */
    protected $orderBy = [];



    public function getSqlString()
    {
        if( ($this->limit > 0 || $this->offset > 0) && $this->_hasJoinMany() ){
            $compiler = new LimitOnSubQueryCompiler($this);
        }else{
            $compiler = new StandardCompiler($this);
        }

        return $compiler->compile();
    }

    /**
     * check if it contains at least 1 hasMany relationship.
     *
     * Useful to know if the query might contain multiple rows for a single result
     * @return bool
     * @throws \Face\Exception\BadParameterException
     * @throws \Face\Exception\FaceElementDoesntExistsException
     * @throws \Face\Exception\RootFaceReachedException
     */
    private function _hasJoinMany(){
        foreach($this->getJoins() as $join){
            $element = $this->getBaseFace()->getElement($join->getPath());
            if($element->hasManyRelationship() || $element->hasManyThroughRelationship()){
                return true;
            }else{
                return false;
            }
        }
        return false;
    }

    /**
     * @return JoinQueryFace
     */
    public function getSoftThroughJoin()
    {
        return $this->softThroughJoin;
    }



    /**
     * @return WhereGroup
     */
    public function getWhere()
    {
        return $this->where;
    }

    public function addWhere(AbstractWhereClause $where)
    {
        if (!$this->where) {
            $this->where = new WhereGroup();
        }
        $this->where->addWhere($where);
    }



    /**
     * @return array
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }


    public function addOrderBy(Field $field){
        $this->orderBy[] = $field;
    }

    /**
     * Be aware that this method wont use the global offset, it will offset for the FROM table only
     * and mays lead to a subquery
     * @param int $offset the offset used for the OFFSET clause of the FROM table
     * @return SelectBuilder
     */
    public function offset($offset)
    {
        $this->offset = $offset;
        return $this;
    }


    /**
     * Be aware that this method wont use the global limit, it will limit on the FROM table only
     * and mays lead to a subquery
     * @param int $limit limit used for the LIMIT clause
     * @param int $offset optionally you can pass the offset in this method. It's aimed to mimic the ``LIMIT 2,10`` syntax
     * @return SelectBuilder
     */
    public function limit($limit, $offset=null)
    {
        $this->limit = $limit;
        if(null !== $offset){
            $this->offset = $offset;
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return mixed
     */
    public function getOffset()
    {
        return $this->offset;
    }


}
