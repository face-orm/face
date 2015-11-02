<?php

namespace Face\Sql\Query\Clause;


use Face\Sql\Query\QueryInterface;

class Group implements SqlClauseInterface {

    /**
     * @var SqlClauseInterface[]
     */
    protected $items = [];

    /**
     * @param SqlClauseInterface $item
     */
    public function addItem(SqlClauseInterface $item)
    {
        $this->items[] = $item;
    }

    /**
     * @inheritdoc
     */
    public function getSqlString(QueryInterface $q)
    {
        $string = "";

        foreach($this->items as $item){
            $clauseString = $item->getSqlString($q);
            if($clauseString){
                $string .= " " . $clauseString;
            }
        }

        return ltrim($string);

    }

}
