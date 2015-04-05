<?php

namespace Face\Sql\Reader\QueryArrayReader;


use Face\Core\EntityFace;
use Face\Sql\Query\Clause\Select\Column;
use Face\Sql\Query\SelectBuilder\JoinQueryFace;
use Face\Sql\Query\SelectBuilder\QueryFace;

class SoftPreparedFace {



    const OPERATION_PASS=0;
    const OPERATION_FORWARD_JOIN=1;
    const OPERATION_IMPLIED=2;
    const OPERATION_EXISTING_ENTITY=3;
    const OPERATION_DO_VALUES=4;

    /**
     * @var EntityFace
     */
    protected $face;

    /**
     * @var QueryFace
     */
    protected $queryFace;

    protected $path;

    /**
     * @var PreparedOperations
     */
    protected $preparedOperation;

    /**
     * @var Column[]
     */
    protected $columns;
    protected $rowIdentityCb;

    function __construct(QueryFace $queryFace, PreparedOperations $preparedOperation)
    {
        $this->queryFace = $queryFace;
        $this->face = $queryFace->getFace();
        $this->path = $queryFace->getPath();
        $this->preparedOperation = $preparedOperation;
    }

    protected function _build()
    {
        $this->columns = $this->queryFace->getColumnsReal();
        $this->rowIdentityCb = $this->_compileRowIdentity();
    }

    /**
     * @return QueryFace
     */
    public function getQueryFace()
    {
        return $this->queryFace;
    }


    protected function _compileRowIdentity(){

        $primaries = $this->face->getPrimaries();

        $str = "";

        foreach($primaries as $p){
            $name = $this->columns[$this->queryFace->makePath($p->getName())]->getAlias();
            $str .= '$row["'. $name .'"].';
        }
        // TODO : OK for performances, but should fix security issue
        return create_function('$row','return '. rtrim($str,".") .';');

    }

    public function getFace(){
        return $this->face;
    }


    public function rowIdentity($row){
        $cb = $this->rowIdentityCb;
        return $cb ($row);
    }


}
