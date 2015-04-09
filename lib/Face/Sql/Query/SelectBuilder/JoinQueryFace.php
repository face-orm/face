<?php

namespace Face\Sql\Query\SelectBuilder;


class JoinQueryFace extends QueryFace {

    protected $isSoft = false;

    /**
     * @return boolean
     */
    public function isSoft()
    {
        return $this->isSoft;
    }

    /**
     * @param boolean $isSoft
     */
    public function setIsSoft($isSoft)
    {
        $this->isSoft = $isSoft;
    }




}