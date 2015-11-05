<?php
/**
 * Created by PhpStorm.
 * User: bob
 * Date: 11/5/15
 * Time: 1:08 PM
 */

namespace Face\Sql\Query\Clause;


use Face\Sql\Query\Clause\Where\WhereGroup;
use Face\Sql\Query\QueryInterface;

class Having implements SqlClauseInterface
{

    /**
     * @var WhereGroup
     */
    protected $condition;

    public function __construct(WhereGroup $condition){
        // TODO: refactor whereGroup to condition that is common to where and having
        // TODO: use columns for where ?
        $this->condition = $condition;
    }

    public function getSqlString(QueryInterface $q)
    {

        $w = $this->condition->getSqlString($q);

        if (empty($w)) {
            return "";
        }

        return "HAVING " . $w;

    }


}
