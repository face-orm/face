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

class Column implements SqlClauseInterface {

    protected $path;
    protected $alias;
    /**
     * @var EntityFaceElement
     */
    protected $entityFaceElement;

    function __construct($parentPath, $alias, EntityFaceElement $entityFaceElement)
    {
        $this->parentPath = $parentPath;
        $this->alias = $alias;
        $this->entityFaceElement = $entityFaceElement;
    }

    public function getSqlString(FQuery $fQuery)
    {
        return
            $fQuery->_doFQLTableName($this->parentPath, null, true) . '.' . $this->entityFaceElement->getSqlColumnName(true)
            . " AS " . $this->getAlias(true);
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->parentPath . "." . $this->entityFaceElement->getName();
    }

    /**
     * @return mixed
     */
    public function getAlias($escaped = false)
    {
        if($escaped){
            return "`" . $this->alias . "`";
        }else{
            return $this->alias;
        }
    }

    /**
     * @return EntityFaceElement
     */
    public function getEntityFaceElement()
    {
        return $this->entityFaceElement;
    }




}