<?php

namespace Face\Sql\Query\Clause\Select\Column;

use Face\Core\EntityFaceElement;
use Face\Sql\Query\Clause\Select\Column;
use Face\Sql\Query\FQuery;

class ElementColumn extends Column
{

    function __construct($parentPath, EntityFaceElement $entityFaceElement)
    {
        parent::__construct($parentPath);
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

    public function getSqlPath()
    {
        return
            FQuery::__doFQLTableNameStatic($this->parentPath, null, true)
            . '.' . $this->entityFaceElement->getSqlColumnName(true);
    }

    public function getPath()
    {
        return $this->parentPath . "." . $this->entityFaceElement->getName();
    }


}
