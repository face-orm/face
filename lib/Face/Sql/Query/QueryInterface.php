<?php

namespace Face\Sql\Query;


use Face\ContextAwareInterface;
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

    /**
     * replaces the waved string by their sql valid column name
     * @param $string
     */
    public function parseColumnNames($string, ContextAwareInterface $context);

}

