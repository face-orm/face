<?php

namespace Face\Sql\Reader\QueryArrayReader;


use Face\Sql\Query\FQuery;

class PreparedOperations extends \ArrayObject{

    protected $identifiersByPath;

    /**
     * @var FQuery
     */
    public $fQuery;

    function __construct($fQuery)
    {
        $this->fQuery = $fQuery;
        $this->_build();


    }

    protected function _build(){
        $faceList = $this->fQuery->getAvailableFaces();
        //parsing from the end allows to ensure existence of children when parents are created. Because children are at the end
        $faceList = array_reverse($faceList);
        foreach ($faceList as $k=>$face) {
            $this[$k] = new PreparedFace($k, $face, $this);
        }

        foreach ($this as $pFace) {
            $pFace->_build();
        }

    }

}