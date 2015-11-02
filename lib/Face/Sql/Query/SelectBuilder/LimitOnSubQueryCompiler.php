<?php


namespace Face\Sql\Query\SelectBuilder;


use Face\Sql\Query\Clause\GroupBy;
use Face\Sql\Query\Clause\SubQuery;
use Face\Sql\Query\SelectBuilder;
use Face\Sql\Query\Clause\From;
use Face\Sql\Query\Clause\Group;
use Face\Sql\Query\Clause\Join;
use Face\Sql\Query\Clause\Limit;
use Face\Sql\Query\Clause\Offset;
use Face\Sql\Query\Clause\OrderBy;
use Face\Sql\Query\Clause\Select;
use Face\Sql\Query\Clause\Where;
use Face\Sql\Query\SelectInterface;
use Face\Sql\Query\SelectQuery;

class LimitOnSubQueryCompiler {

    /**
     * @var SelectBuilder
     */
    protected $selectBuilder;

    function __construct(SelectInterface $selectBuilder)
    {
        $this->selectBuilder = $selectBuilder;
    }


    public function compile(){

        /* @var $facesToSelect QueryFace[] */
        $facesToSelect["this"] = $this->selectBuilder->getBaseQueryFace();
        $facesToSelect = array_merge($facesToSelect, $this->selectBuilder->getJoins());


        // SELECT
        // columns
        $columns = [];
        $primariesColumns = [];
        foreach($facesToSelect as $queryFace){
            foreach($queryFace->getColumnsReal() as $column){
                $columns[] = $column;
            }
        }

        foreach($facesToSelect["this"]->getColumnsReal() as $column){
            $columns[] = $column;

            if($column->getEntityFaceElement()->isPrimary()){
                $primariesColumns[] = $column;
            }
        }

        $selectClause = new Select($columns);


        // SELECT id for subquery
        $selectClauseSubquery = new Select($primariesColumns);


        // FROM
        $fromClause = new From($this->selectBuilder->getBaseFace());


        $joinGroup = new Group();
        // JOINs
        foreach ($this->selectBuilder->getJoins() as $joinQueryFace) {
            $join = new Join($this->selectBuilder->getBaseFace(), $joinQueryFace);
            $joinGroup->addItem($join);
        }


        // SOFT JOINs
        if (is_array($this->selectBuilder->getSoftThroughJoin())) {
            foreach ($this->selectBuilder->getSoftThroughJoin() as $path => $joinQueryFace) {
                if (!$this->selectBuilder->isJoined($path)) {
                    $join = new Join($this->selectBuilder->getBaseFace(), $joinQueryFace);
                    $joinGroup->addItem($join);
                }
            }
        }

        // WHERE SUBQUERY
        $whereGroup = $this->selectBuilder->getWhere();
        if ($whereGroup) {
            $whereSubquery = new Where($whereGroup);
        }else{
            $whereSubquery = null;
        }


        // GROUP BY SUBQUERY
        $groupBySubquery = new GroupBy($primariesColumns);


        // ORDER
        $orders = $this->selectBuilder->getOrderBy();
        $orderByClause = new OrderBy();
        foreach($orders as $order){
            $orderByClause->addItem($order);
        }

        // LIMIT
        $limit = new Limit($this->selectBuilder->getLimit());

        // OFFSET
        $offset = new Offset($this->selectBuilder->getOffset());

        // BUILD SUBQUERY
        $subqueryGroup = new Group();
        $subqueryGroup->addItem($selectClauseSubquery);
        $subqueryGroup->addItem($fromClause);
        $subqueryGroup->addItem($joinGroup);
        if($whereSubquery) {
            $subqueryGroup->addItem($whereSubquery);
        }
        $subqueryGroup->addItem($groupBySubquery);
        $subqueryGroup->addItem($orderByClause);
        $subqueryGroup->addItem($limit);
        $subqueryGroup->addItem($offset);








        // WHERE QUERY
        $whereIn = new Where\WhereInSubquery($subqueryGroup, $primariesColumns);
        $whereQuery = new Where($whereIn);

        // BUILDER QUERY
        $queryGroup = new Group();
        $queryGroup->addItem($selectClause);
        $queryGroup->addItem($fromClause);
        $queryGroup->addItem($joinGroup);
        $queryGroup->addItem($whereQuery);
        $queryGroup->addItem($orderByClause);



        $sqlQ = $queryGroup->getSqlString($this->selectBuilder);

        return $sqlQ;
    }


}
