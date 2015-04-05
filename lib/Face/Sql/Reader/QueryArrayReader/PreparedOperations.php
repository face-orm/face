<?php

namespace Face\Sql\Reader\QueryArrayReader;


use Face\Sql\Query\FQuery;

class PreparedOperations{

    protected $identifiersByPath;

    /**
     * @var PreparedFace[]
     */
    protected $preparedFaces;

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
        $faceList = $this->fQuery->getAvailableQueryFaces();
        //parsing from the end allows to ensure existence of children when parents are created. Because children are at the end
        $faceList = array_reverse($faceList);
        foreach ($faceList as $k => $queryFace) {
            $this->preparedFaces[$k] = new PreparedFace($queryFace, $this);
        }

        foreach ($this->preparedFaces as $pFace) {
            $pFace->_build();
        }

    }

    /**
     * @return PreparedFace[]
     */
    public function getPreparedFaces()
    {
        return $this->preparedFaces;
    }

}