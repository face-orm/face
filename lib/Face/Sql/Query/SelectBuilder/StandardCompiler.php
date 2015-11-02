<?php

namespace Face\Sql\Query\SelectBuilder;
use Face\Sql\Query\Clause\From;
use Face\Sql\Query\Clause\Group;
use Face\Sql\Query\Clause\Join;
use Face\Sql\Query\Clause\Limit;
use Face\Sql\Query\Clause\Offset;
use Face\Sql\Query\Clause\OrderBy;
use Face\Sql\Query\Clause\Select;
use Face\Sql\Query\Clause\Where;
use Face\Sql\Query\SelectBuilder;
use Face\Sql\Query\FQuery;
use Face\Sql\Query\SelectInterface;
use Face\Sql\Query\SelectQuery;

/**
 * Class Compiler
 *
 * This class helps to compile a SelectBuilder.
 * It aims to make the selectBuilder class more clean by migrating compile job here
 *
 * @package Face\Sql\Query\SelectBuilder
 */
class StandardCompiler {

    /**
     * @var SelectInterface
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
        $columns = [];
        foreach($facesToSelect as $queryFace){
            foreach($queryFace->getColumnsReal() as $column){
                $columns[] = $column;
            }
        }

        $queryBuilder = new Group();

        // SELECT
        $selectClause = new Select($columns);
        $queryBuilder->addItem($selectClause);


        // FROM
        $fromClause = new From($this->selectBuilder->getBaseQueryFace()->getFace());
        $queryBuilder->addItem($fromClause);


        // JOINs
        foreach ($this->selectBuilder->getJoins() as $joinQueryFace) {
            $join = new Join($this->selectBuilder->getBaseQueryFace()->getFace(), $joinQueryFace);
            $queryBuilder->addItem($join);
        }


        // SOFT JOINs
        foreach ($this->selectBuilder->getSoftThroughJoin() as $path => $joinQueryFace) {
            if (!$this->selectBuilder->isJoined($path)) {
                $join = new Join($this->selectBuilder->getBaseQueryFace()->getFace(), $joinQueryFace);
                $queryBuilder->addItem($join);
            }
        }


        // WHERE
        $whereGroup = $this->selectBuilder->getWhere();
        if ($whereGroup) {
            $where = new Where($whereGroup);
            $queryBuilder->addItem($where);
        }

        // ORDER
        $orders = $this->selectBuilder->getOrderBy();
        $orderByClause = new OrderBy();
        foreach($orders as $order){
            $orderByClause->addItem($order);
        }
        $queryBuilder->addItem($orderByClause);

        // LIMIT
        $limit = new Limit($this->selectBuilder->getLimit());
        $queryBuilder->addItem($limit);

        // OFFSET
        $offset = new Offset($this->selectBuilder->getOffset());
        $queryBuilder->addItem($offset);


        $sqlQ = $queryBuilder->getSqlString($this->selectBuilder);


        return $sqlQ;
    }

}
