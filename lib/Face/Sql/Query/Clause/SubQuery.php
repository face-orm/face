<?php

namespace Face\Sql\Query\Clause;


use Face\Sql\Query\FQuery;

class SubQuery implements SqlClauseInterface {

    /**
     * @var SqlClauseInterface
     */
    protected $item;

    /**
     * @param SqlClauseInterface $item
     */
    public function addItem(SqlClauseInterface $item)
    {
        $this->item = $item;
    }

    /**
     * @inheritdoc
     */
    public function getSqlString(FQuery $q)
    {
        $string =  $this->item->getSqlString($q);
        return "($string)";
    }

}