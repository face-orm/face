<?php

namespace Face\Sql\Query;

use Face\Config;
use Face\Util\OOPUtils;

/**
 * Provide a way for face to do delete ignoring join.
 *
 * @author sghzal
 */
class SimpleDelete extends FQuery
{
 
    protected $entity;

    protected $config;

    public function __construct($entity,Config $config = null)
    {

        if(!$config){
            $config = Config::getDefault();
        }
        $this->config = $config;

        if (!OOPUtils::UsesTrait($entity, 'Face\Traits\EntityFaceTrait')) {
            throw new \Exception("Class ".get_class($entity)." doesnt use the trait \Face\Traits\EntityFaceTrait");
        }
        
        $this->entity = $entity;
        
        parent::__construct($entity->getEntityFace($config->getFaceLoader()));
    }

    
    public function getSqlString()
    {
        $baseFace = $this->getBaseFace();

        $where="";
        $i=0;
        foreach ($baseFace as $elm) {
            if ($elm->isValue()) {
                /* @var $elm \Face\Core\EntityFaceElement */
                if ($elm->isValue() && !$elm->isPrimary()) {

                } else {
                    if ($i>0) {
                        $where.=" AND ";
                    } else {
                        $i++;
                    }
                    $where.=$elm->getSqlColumnName()."=:".$elm->getSqlColumnName();
                    $this->bindValue(":" . $elm->getSqlColumnName(), $this->entity->faceGetter($elm));
                }

            }


        }
        
        $queryStr= "DELETE FROM ".$baseFace->getSqlTable(). " WHERE ".$where." LIMIT 1";

        return $queryStr;
        
    }
}
