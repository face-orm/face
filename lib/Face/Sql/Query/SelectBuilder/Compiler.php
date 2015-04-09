<?php

namespace Face\Sql\Query\SelectBuilder;
use Face\Sql\Query\Clause\From;
use Face\Sql\Query\Clause\Group;
use Face\Sql\Query\Clause\Join;
use Face\Sql\Query\Clause\OrderBy;
use Face\Sql\Query\Clause\Select;
use Face\Sql\Query\Clause\Where;
use Face\Sql\Query\SelectBuilder;
use Face\Sql\Query\FQuery;

/**
 * Class Compiler
 *
 * This class helps to compile a SelectBuilder.
 * It aims to make the selectBuilder class more clean by migrating compile job here
 *
 * @package Face\Sql\Query\SelectBuilder
 */
class Compiler {

    /**
     * @var SelectBuilder
     */
    protected $selectBuilder;

    function __construct(SelectBuilder $selectBuilder)
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
        $fromClause = new From($this->selectBuilder->getBaseFace());
        $queryBuilder->addItem($fromClause);


        // JOINs
        foreach ($this->selectBuilder->getJoins() as $joinQueryFace) {
            $join = new Join($this->selectBuilder->getBaseFace(), $joinQueryFace);
            $queryBuilder->addItem($join);
        }


        // SOFT THROUGH JOINs
        // Soft join
        if (is_array($this->selectBuilder->getSoftThroughJoin())) {
            foreach ($this->selectBuilder->getSoftThroughJoin() as $path => $joinQueryFace) {
                if (!$this->selectBuilder->isJoined($path)) {
                    $join = new Join($this->selectBuilder->getBaseFace(), $joinQueryFace);
                    $queryBuilder->addItem($join);
                }
            }
        }

        $whereGroup = $this->selectBuilder->getWhere();
        if ($whereGroup) {
            $where = new Where($whereGroup);
            $queryBuilder->addItem($where);
        }


        $orders = $this->selectBuilder->getOrderBy();
        $orderByClause = new OrderBy();
        foreach($orders as $order){
            $orderByClause->addItem($order);
        }
        $queryBuilder->addItem($orderByClause);


        $sqlQ = $queryBuilder->getSqlString($this->selectBuilder);

//        $fromLimit = $this->selectBuilder->getBaseQueryFace()->getLimit();
//        $fromOffset = $this->selectBuilder->getBaseQueryFace()->getOffset();

        return $sqlQ;
    }



    /**
     * @param string $path
     * @param string $token the token to use for separate elements of the path. Default  $this->getDotToken() will be used
     * @return string
     */
    public function _doFQLTableName($path, $escape = false)
    {
        return FQuery::__doFQLTableNameStatic($path, ".", $escape);
    }




}