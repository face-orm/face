<?php

namespace Face\Sql\Query\Clause\OrderBy;

use Face\Core\EntityFace;
use Face\Sql\Query\Clause\SqlClauseInterface;
use Face\Sql\Query\FQuery;

class Field implements SqlClauseInterface{

    protected $field;
    protected $direction;
    /**
     * @var EntityFace
     */
    protected $baseFace;

    function __construct($baseFace, $field, $direction)
    {
        $this->field = $field;
        $this->direction = $direction;
        $this->baseFace = $baseFace;
    }

    public function getSqlString(FQuery $q)
    {
        $str = "";

        try {
            $parentFace = $this->baseFace->getElement($this->field, 1, $pieceOfPath)->getFace();
        } catch (\Face\Exception\RootFaceReachedException $e) {
            $pieceOfPath[0] = "";
            $pieceOfPath[1] = $this->field;
            $parentFace = $this->baseFace;
        }
        $childElement = $parentFace->getElement($pieceOfPath[1]);

        $str .= FQuery::__doFQLTableNameStatic($pieceOfPath[0], null, true) . "." . $childElement->getSqlColumnName(true) . " " . $this->direction;

        return $str;

    }


}