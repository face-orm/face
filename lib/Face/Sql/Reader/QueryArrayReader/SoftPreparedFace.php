<?php

namespace Face\Sql\Reader\QueryArrayReader;


use Face\Core\EntityFace;

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

    protected $path;

    /**
     * @var PreparedOperations
     */
    protected $preparedOperation;

    protected $columnNames;
    protected $rowIdentityCb;

    function __construct($path, EntityFace $face, PreparedOperations $preparedOperation)
    {
        $this->face = $face;
        $this->path = $path;
        $this->preparedOperation = $preparedOperation;
    }

    protected function _build()
    {
        foreach($this->face->getPrimaries() as $e){
            $this->columnNames[$e->getName()] = $this->makeColumnName($e);
        }
        $this->rowIdentityCb = $this->_compileRowIdentity();
    }

    public function makeColumnName(\Face\Core\EntityFaceElement $elm)
    {
        $elmName = $elm->getName();
        $selectColumns = $this->preparedOperation->fQuery->getSelectedColumns();
        if (isset($selectColumns[ $this->path . ".$elmName"])) {
            $name = $selectColumns[$this->path . ".$elmName"];
        } else {
            $name = $this->preparedOperation->fQuery->_doFQLTableName($this->path . "." . $elmName);
        }
        return $name;

    }

    protected function _compileRowIdentity(){

        $primaries=$this->face->getPrimaries();

        $str = "";

        foreach($primaries as $p){

            $name = $this->columnNames[$p->getName()];

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
