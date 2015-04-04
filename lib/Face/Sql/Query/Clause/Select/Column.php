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

    function __construct($path, $alias, EntityFaceElement $entityFaceElement)
    {
        $this->path = $path;
        $this->alias = $alias;
        $this->entityFaceElement = $entityFaceElement;
    }

    public function getSqlString(FQuery $q)
    {

    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return mixed
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @return EntityFaceElement
     */
    public function getEntityFaceElement()
    {
        return $this->entityFaceElement;
    }




}