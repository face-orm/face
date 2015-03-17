<?php

namespace Face\Sql\Query;

use Face\Core\EntityFace;
use Face\Core\EntityFaceElement;
use Face\Debugger;
use Face\Sql\Query\Clause\Where;
use Face\Sql\Query\Clause\WhereInterface;
use Face\Traits\ContextAwareTrait;
use Face\Traits\EntityFaceTrait;

/**
 * Description of QueryBuilder
 *
 * @author bobito
 */
class SelectBuilder extends \Face\Sql\Query\FQuery
{

    use ContextAwareTrait;

    const RELATION_JOIN = 0;
    const RELATION_JOIN_USE_DATA = 1;
    const RELATION_USE_DATA = 2;


    /**
     *
     * @var Where\WhereGroup
     */
    protected $where;

    /**
     * used by whereINRelation() to add join to the final query without parsing them
     * @var array
     */
    protected $softThroughJoin;


    /**
     * used to generate unique bound params for whereIn method
     * @var int
     */
    private $whereInCount=0;

    protected $offset=0;
    protected $limit=0;

    protected $fromLimit;
    protected $fromOffset;

    function __construct(EntityFace $baseFace)
    {
        parent::__construct($baseFace);
    }




    public function getSqlString()
    {

        $sqlQ=$this->prepareSelectClause();
        $sqlQ.=" ".$this->prepareFromClause();
        $sqlQ.=" ".$this->prepareJoinClause();
        $sqlQ.=" ".$this->prepareWhereClause();

        $sqlQ = rtrim($sqlQ);


        if( ($this->fromLimit > 0 || $this->fromOffset > 0) && count($this->joins) == 0) {

            if ($this->fromLimit > 0) {
                $sqlQ .= " LIMIT " . $this->fromLimit;
            }


            if ($this->fromOffset > 0) {
                $sqlQ .= " OFFSET " . $this->fromOffset;
            }
        }

        return $sqlQ;

    }

    /**
     * Be aware that this method wont use the global offset, instead it will be used into the FROM clause
     * @param int $offset the offset used for the OFFSET clause of the FROM table
     */
    public function setOffset($offset)
    {
        $this->fromOffset = intval($offset);
    }


    /**
     * Be aware that this method wont use the global limit, instead it will be used into the FROM clause
     *
     *
     * @param int $limit limit used for the LIMIT clause
     * @param int $offset optionnaly you can pass the offset in this method. It's aimed to mimic the ``LIMIT 2,10`` syntax
     */
    public function setLimit($limit, $offset=null)
    {
        $this->fromLimit = intval($limit);
        if(null !== $offset){
            $this->setOffset($offset);
        }
    }


    /**
     * add a join clause to the query
     * @param string $path the face path to the join
     * @return \Face\Sql\Query\SelectBuilder it returns itSelf
     */
    public function join($path)
    {

        $path = $this->getNameInContext($path);

        $this->joins[$this->_doFQLTableName($path, ".")] = $this->baseFace
            ->getElement($path)
            ->getFace();

        return $this;
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

    public function addWhere(WhereInterface $where)
    {
        if (!$this->where) {
            $this->where = new Where\WhereGroup();
        }
        $this->where->addWhere($where);
    }

    /**
     * set the where clause
     * @param string $whereString the FQuery formated  where clause
     * @return \Face\Sql\Query\SelectBuilder   it returns itSelf
     */
    public function where($whereString)
    {

        $where = new Where\WhereString($whereString);
        $where->context($this->getContext());

        $this->addWhere($where);

        return $this;
    }

    /**
     * appends an AND part to the current where clause
     * @param string $whereString the FQuery formated  where clause
     * @return \Face\Sql\Query\SelectBuilder   it returns itSelf
     */
    public function whereAND($whereString)
    {

        $where = new Where\WhereString($whereString);
        $where->context($this->getContext());

        $this->addWhere($where);

        return $this;
    }

    /**
     * appends an OR part to the current where clause
     * @param string $whereString the FQuery formated  where clause
     * @return \Face\Sql\Query\SelectBuilder   it returns itSelf
     */
    public function whereOR($whereString)
    {
        $where = new Where\WhereString($whereString);
        $where->context($this->getContext());

        $this->addWhere($where, "OR");
        return $this;
    }

    /**
     * creates Where $fieldName in (:$array[0],:$array[1],...) and generates some bindValue
     * this is identical to the sql  ``WHERE columnName IN (...)``
     * @param string $fieldName  name of the column (can make use of dynamic creation with ~dynColName)
     * @param array $array  list of values to bind
     * @throws \Exception
     * @return \Face\Sql\Query\SelectBuilder
     */
    public function whereIN($path, $array, $logic = null)
    {
        $fieldName = $this->getNameInContext($path);
        $this->_whereInRaw($fieldName, $array, $logic);
        return $this;
    }

    protected function _whereInRaw($fieldName, $array, $logic = null)
    {
        $bindString = "";
        foreach ($array as $value) {
            $bindString.=',:fautoIn'.++$this->whereInCount;
            $this->bindValue(':fautoIn'.$this->whereInCount, $value);
        }
        $phrase = "$fieldName IN (" . ltrim($bindString, ",") . ")";
        $where = new Where\WhereString($phrase);
        $this->addWhere($where, $logic);
    }

    /**
     * @param string $relation the name of the direct element (not a path). e.g : "lemon" is accepted "lemon.tree" is not
     * @param EntityFaceTrait[] $entities
     * @return $this
     * @throws \Exception
     */
    public function whereINRelation($relation, $entities, $logic = null)
    {

        $nsrelation = $this->getNameInContext($relation);

        $relatedElement = $this->baseFace->getDirectElement($relation);
        $join = $relatedElement->getSqlJoin();

        if (count($join) > 1) {
            // todo
            throw new \Exception("WhereINRelation with many join columns not implemented yet");

        } elseif (!empty($join)) {
            if ($relatedElement->hasManyThroughRelationship()) {
                $itsColumn = current($relatedElement->getFace()->getDirectElement($relatedElement->getRelatedBy())->getSqlJoin());
                $this->softThroughJoin[$this->_doFQLTableName($nsrelation, ".")] = $relatedElement->getFace();
                $values = $this->__whereINRelationOneIdentifierGetValues($entities, null, $relatedElement);
                $this->_whereInRaw($this->_doFQLTableName("$relation.through", null, true) . ".`$itsColumn`", $values);
            } else {
                $myColumn  = key($join);
                $itsColumn = $join[$myColumn];
                $values = $this->__whereINRelationOneIdentifierGetValues($entities, $itsColumn, $relatedElement);
                $this->_whereInRaw( "`$myColumn`", $values, $logic);
            }

        } else {
            throw new \Exception("There is no sql join for : " . $this->baseFace->getClass() . "." . $nsrelation);
        }

        return $this;
    }



    protected function __whereINRelationOneIdentifierGetValues($entities, $itsColumn, EntityFaceElement $relatedElement)
    {

        $values = array();

        if ($relatedElement->hasManyThroughRelationship()) {
            foreach ($entities as $e) {
                $values[] = $e->faceGetIdentity();
            }
        } elseif ($relatedElement->relationIsBelongsTo()) {
            foreach ($entities as $e) {
                $values[] = $e->faceGetter($itsColumn);
            }
        } elseif ($relatedElement->relationIsHas___()) {
            foreach ($entities as $e) {
                $v = $e->faceGetter($itsColumn);
                $values[$v] = $v;
            }
            $values = array_values($values);
        } else {
            throw new \Exception("unknown relation : " . $relatedElement->getRelation());
        }

        return $values;
    }

    public function prepareSelectClause()
    {


        $facesToSelect["this"]=$this->baseFace;
        $facesToSelect=  array_merge($facesToSelect, $this->joins);

        $selectFields=[];

        foreach ($facesToSelect as $path => $face) {
            $truePath = $this->_doFQLTableName($path);
            foreach ($face as $elm) {
                /* @var $elm \Face\Core\EntityFaceElement */
                if ($elm->isValue()) {
                    $selectFields[]= "`" . $truePath . "`." . $elm->getSqlColumnName(true) . " AS `" . $truePath . $this->dotToken . $elm->getName() . "`";
                }
            }
        }

        $sql="SELECT ";
        $sql.=implode(",", $selectFields);


        return $sql;
    }

    public function prepareFromClause()
    {

        $baseTable = $this->baseFace->getSqlTable(true);

        $table = "";

        if( ($this->fromLimit > 0 || $this->fromOffset > 0) && count($this->joins)>0){

                $table = "SELECT * FROM $baseTable";
                if($this->fromLimit>0){
                    $table .= " LIMIT " . $this->fromLimit;
                }
                if($this->fromOffset>0){
                    $table .= " OFFSET " . $this->fromOffset;
                }

                $table = "($table)";

        }else{
            $table = $baseTable;
        }

        return "FROM " . $table . " AS `this`";
    }

    public function prepareJoinClause()
    {
        $sql = "";
        foreach ($this->joins as $path => $face) {
            $sql .= $this->__prepareJoinClauseFor($path, $face, false);
        }

        // Soft join
        if (is_array($this->softThroughJoin)) {
            foreach ($this->softThroughJoin as $path => $face) {
                if (!$this->isJoined($path)) {
                    $sql.=$this->__prepareJoinClauseFor($path, $face, true);
                }
            }
        }

        return $sql;
    }

    private function __prepareJoinClauseFor($path, EntityFace $face, $isSoft)
    {

        $joinSql = "";
        try {
            $parentFace=$this->baseFace->getElement($path, 1, $pieceOfPath)->getFace();
        } catch (\Face\Exception\RootFaceReachedException $e) {
            $pieceOfPath[0]="";
            $pieceOfPath[1]=$path;
            $parentFace=$this->baseFace;
        }

        $childElement=$parentFace->getElement($pieceOfPath[1]);

        $joinSql = FQuery::__doFQLJoinTable($path, $face, $parentFace, $childElement, $pieceOfPath[0], $this->dotToken, $isSoft);

        return $joinSql;
    }

    public function prepareWhereClause()
    {
        if (null===$this->where) {
            return "";
        }

        $w = $this->where->getSqlString($this);

        if (empty($w)) {
            return "";
        }


        return "WHERE " . $w;
    }
}
