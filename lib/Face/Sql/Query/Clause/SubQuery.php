<?php

namespace Face\Sql\Query\Clause;



use Face\Sql\Query\QueryInterface;

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
    public function getSqlString(QueryInterface $q)
    {
        $string =  $this->item->getSqlString($q);
        return "($string)";
    }

}
