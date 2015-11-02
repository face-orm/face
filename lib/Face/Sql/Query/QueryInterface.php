<?php

namespace Face\Sql\Query;


use Face\Sql\Query\SelectBuilder\QueryFace;

interface QueryInterface
{

    /**
     * @return string
     */
    public function getSqlString();

    /**
     * @return QueryFace
     */
    public function getBaseQueryFace();

}

