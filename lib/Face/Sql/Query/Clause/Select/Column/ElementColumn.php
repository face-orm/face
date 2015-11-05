<?php

namespace Face\Sql\Query\Clause\Select\Column;

use Face\Core\EntityFaceElement;
use Face\Sql\Query\Clause\Select\Column;
use Face\Sql\Query\FQuery;
use Face\Sql\Query\QueryInterface;

class ElementColumn extends Column
{

    protected $parentPath;

    /**
     * @var EntityFaceElement
     */
    protected $entityFaceElement;

    function __construct($parentPath, EntityFaceElement $entityFaceElement)
    {
        $this->parentPath = $parentPath;
        $this->entityFaceElement = $entityFaceElement;
    }

    /**
     * @return EntityFaceElement
     */
    public function getEntityFaceElement()
    {
        return $this->entityFaceElement;
    }

    public function isHydratable(){
        return true;
    }

    public function getHydrationAlias(){
        return $this->entityFaceElement->getPropertyName();
    }

    public function getSqlStatement(QueryInterface $queryInterface)
    {
        return
            FQuery::__doFQLTableNameStatic($this->parentPath, null, true)
            . '.' . $this->entityFaceElement->getSqlColumnName(true);
    }




}
