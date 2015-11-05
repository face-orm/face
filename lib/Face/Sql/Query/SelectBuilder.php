<?php

namespace Face\Sql\Query;

use Face\Core\EntityFace;
use Face\Core\EntityFaceElement;
use Face\Debugger;
use Face\Sql\Query\Clause\OrderBy;
use Face\Sql\Query\Clause\Where;
use Face\Sql\Query\SelectBuilder\Compiler;
use Face\Sql\Query\SelectBuilder\JoinQueryFace;
use Face\Sql\Query\SelectBuilder\LimitOnSubQueryCompiler;
use Face\Sql\Query\SelectBuilder\QueryFace;
use Face\Sql\Query\SelectBuilder\StandardCompiler;
use Face\Traits\ContextAwareTrait;
use Face\Traits\EntityFaceTrait;

/**
 * Description of QueryBuilder
 *
 * @author bobito
 */
class SelectBuilder extends SelectQuery
{

    use ContextAwareTrait;

    const RELATION_JOIN = 0;
    const RELATION_JOIN_USE_DATA = 1;
    const RELATION_USE_DATA = 2;

    const ORDER_ASC  = "ASC";
    const ORDER_DESC = "DESC";


    /**
     * used to generate unique bound params for whereIn method
     * @var int
     */
    private $whereInCount=0;

    /**
     * @var Where\WhereGroup
     */
    protected $whereGroup;

    function __construct(EntityFace $baseFace, $columns = [])
    {
        parent::__construct($baseFace);
        $this->fromQueryFace->setColumns($columns);
    }

    /**
     * @return string
     */
    public function getDotToken()
    {
        return $this->dotToken;
    }

    /**
     * add a join clause to the query
     * @param string $path the face path to the join
     * @return SelectBuilder
     */
    public function join($path, $select = null)
    {

        $path = $this->getNameInContext($path);

        $face = $this->getBaseFace()
            ->getElement($path)
            ->getFace();

        $sqlPath = $this->_doFQLTableName($path, ".");

        $qJoin = new JoinQueryFace($sqlPath, $face);

        if(null !== $select){
            $qJoin->setColumns($select);
        }

        $this->addJoin($qJoin);
        return $this;
    }


    public function addWhere(Where\AbstractWhereClause $whereCondition, $logic = null)
    {
        if (!$this->where) {
            $this->whereGroup = new Where\WhereGroup();
            $where = new Where($this->whereGroup);
            $this->setWhere($where);
        }
        $this->whereGroup->addWhere($whereCondition, $logic);
    }



    public function orderBy($field, $direction = null){
        $directionUpper = strtoupper($direction);

        if(null == $direction){
            $directionUpper = self::ORDER_ASC;
        }else if($directionUpper !== self::ORDER_DESC && $directionUpper !== self::ORDER_ASC){
            throw new \Exception("Value '$direction' for direction is not valid. Please use '" . self::ORDER_ASC . "' or '" . self::ORDER_DESC . "' ");
        }

        $this->addOrderBy(new OrderBy\Field($this->getBaseFace(), $field, $directionUpper));
        return $this;
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

        $relatedElement = $this->getBaseFace()->getDirectElement($relation);
        $join = $relatedElement->getSqlJoin();

        if (count($join) > 1) {
            // todo
            throw new \Exception("WhereINRelation with many join columns not implemented yet");

        } elseif (!empty($join)) {
            if ($relatedElement->hasManyThroughRelationship()) {
                $itsColumn = current($relatedElement->getFace()->getDirectElement($relatedElement->getRelatedBy())->getSqlJoin());

                $sqlPath = $this->_doFQLTableName($nsrelation, ".");

                $this->softThroughJoin[$sqlPath] = new JoinQueryFace($sqlPath, $relatedElement->getFace(), $this);
                $this->softThroughJoin[$sqlPath]->setIsSoft(true);
                $values = $this->__whereINRelationOneIdentifierGetValues($entities, null, $relatedElement);
                $this->_whereInRaw($this->_doFQLTableName("$relation.through", null, true) . ".`$itsColumn`", $values);
            } else {
                $myColumn  = key($join);
                $itsColumn = $join[$myColumn];
                $values = $this->__whereINRelationOneIdentifierGetValues($entities, $itsColumn, $relatedElement);
                $this->_whereInRaw( "`$myColumn`", $values, $logic);
            }

        } else {
            throw new \Exception("There is no sql join for : " . $this->getBaseFace()->getClass() . "." . $nsrelation);
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


}
