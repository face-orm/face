<?php
/**
 * Created by PhpStorm.
 * User: bob
 * Date: 4/4/15
 * Time: 12:27 PM
 */

namespace Face\Sql\Query\Clause\Select;

use Face\Core\EntityFaceElement;
use Face\Sql\Query\Clause\SqlClauseInterface;
use Face\Sql\Query\FQuery;

abstract class Column implements SqlClauseInterface {

    protected $path;
    protected $queryAlias;
    protected $hydrationAlias;

    function __construct($parentPath)
    {
        $this->parentPath = $parentPath;
    }



    public function getSqlString(FQuery $fQuery)
    {
        $queryAlias = $this->getQueryAlias(true);

        if($queryAlias){
            return $this->getSqlPath() . " AS " . $queryAlias;
        }else{
            return $this->getSqlPath();
        }
    }


    abstract public function getSqlPath();

    abstract public function getPath();

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
