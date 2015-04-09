<?php

namespace Face\Sql\Query\SelectBuilder;
use Face\Sql\Query\Clause\From;
use Face\Sql\Query\Clause\Select;
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

        $selectClause = new Select($columns);
        $fromClause = new From($this->selectBuilder->getBaseFace());




        $sqlQ = $selectClause->getSqlString($this->selectBuilder) . " " . $fromClause->getSqlString($this->selectBuilder) ;

        $join = $this->prepareJoinClause();
        if($join){
            $sqlQ.=" " . $join;
        }

        $where = $this->prepareWhereClause();
        if($where){
            $sqlQ.=" " . $where;
        }

        $order = $this->prepareOrderByClause();
        if($order){
            $sqlQ.=" " . $order;
        }

        $sqlQ = rtrim($sqlQ);

        $fromLimit = $this->selectBuilder->getBaseQueryFace()->getLimit();
        $fromOffset = $this->selectBuilder->getBaseQueryFace()->getOffset();

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


    public function prepareJoinClause()
    {
        $sql = "";
        foreach ($this->selectBuilder->getJoins() as $path => $joinQueryFace) {
            $sql .= $this->__prepareJoinClauseFor( $joinQueryFace, false);
        }



        // Soft join
        if (is_array($this->selectBuilder->getSoftThroughJoin())) {
            foreach ($this->selectBuilder->getSoftThroughJoin() as $path => $joinQueryFace) {
                if (!$this->selectBuilder->isJoined($path)) {
                    $sql.=$this->__prepareJoinClauseFor( $joinQueryFace, true);
                }
            }
        }

        return $sql;
    }

    private function __prepareJoinClauseFor(JoinQueryFace $joinQueryFace, $isSoft)
    {

        $face = $joinQueryFace->getFace();
        $path = $joinQueryFace->getPath();

        $joinSql = "";
        try {
            $parentFace = $this->selectBuilder->getBaseFace()->getElement($path, 1, $pieceOfPath)->getFace();
        } catch (\Face\Exception\RootFaceReachedException $e) {
            $pieceOfPath[0] = "";
            $pieceOfPath[1] = $path;
            $parentFace = $this->selectBuilder->getBaseFace();
        }

        $childElement = $parentFace->getElement($pieceOfPath[1]);

        $joinSql = FQuery::__doFQLJoinTable($path, $face, $parentFace, $childElement, $pieceOfPath[0], $this->selectBuilder->getDotToken(), $isSoft);

        return $joinSql;
    }


    public function prepareWhereClause()
    {
        if (null === $this->selectBuilder->getWhere()) {
            return "";
        }

        $w = $this->selectBuilder->getWhere()->getSqlString($this->selectBuilder);

        if (empty($w)) {
            return "";
        }


        return "WHERE " . $w;
    }

    public function prepareOrderByClause()
    {

        if (count($this->selectBuilder->getOrderBy()) > 0) {
            $str = "ORDER BY";
            $i = 0;
            foreach($this->selectBuilder->getOrderBy() as $orderBy){

                try {
                    $parentFace = $this->selectBuilder->getBaseFace()->getElement($orderBy[0], 1, $pieceOfPath)->getFace();
                } catch (\Face\Exception\RootFaceReachedException $e) {
                    $pieceOfPath[0] = "";
                    $pieceOfPath[1] = $orderBy[0];
                    $parentFace = $this->selectBuilder->getBaseFace();
                }
                $childElement = $parentFace->getElement($pieceOfPath[1]);

                if($i>0){
                    $str.=",";
                }

                $str .= " " . $this->selectBuilder->_doFQLTableName($pieceOfPath[0], null, true) . "." . $childElement->getSqlColumnName(true) . " " . $orderBy[1];
                $i++;
            }

            return $str;
        }else{
            return "";
        }


    }

}