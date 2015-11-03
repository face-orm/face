<?php
/**
 * Created by PhpStorm.
 * User: bob
 * Date: 4/4/15
 * Time: 12:27 PM
 */

namespace Face\Sql\Query\Clause\Select;

use Face\Sql\Query\Clause\SqlClauseInterface;
use Face\Sql\Query\QueryInterface;

abstract class Column implements SqlClauseInterface {

    protected $path;
    protected $queryAlias;
    protected $hydrationAlias;


    public function getSqlString(QueryInterface $fQuery)
    {
        $queryAlias = $this->getQueryAlias(true);

        if($queryAlias){
            return $this->getSqlStatement($fQuery) . " AS " . $queryAlias;
        }else{
            return $this->getSqlStatement($fQuery);
        }
    }



    abstract public function getSqlStatement(QueryInterface $queryInterface);

    abstract public function isHydratable();

    abstract public function getHydrationAlias();

    /**
     * Set an alias that is used as column name in the query
     * @param string $alias
     */
    public function setQueryAlias($alias){
        $this->queryAlias = $alias;
    }

    /**
     * @return mixed
     */
    public function getQueryAlias($escaped = false)
    {
        if($escaped && $this->queryAlias){
            return "`" . $this->queryAlias . "`";
        }else{
            return $this->queryAlias;
        }
    }
}
