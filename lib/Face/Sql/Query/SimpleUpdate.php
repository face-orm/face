<?php

namespace Face\Sql\Query;

use Face\Util\OOPUtils;



/**
 * Provide a way for face to do inserts ignoring join. 
 *
 * @author sghzal
 */
class SimpleUpdate extends FQuery {
 
    protected $entity;


    public function __construct($entity) {
       
        if(!OOPUtils::UsesTrait($entity, 'Face\Traits\EntityFaceTrait' )){
            throw new \Exception("Class ".get_class($entity)." doesnt use the trait \Face\Traits\EntityFaceTrait");
        }
        
        $this->entity = $entity;
        
        parent::__construct($entity->getEntityFace());
    }

    
    public function getSqlString() {
        $baseFace = $this->getBaseFace();
        
        
        $sets="";
        $where="";
        $i=0;
        foreach ($baseFace as $elm){

            if($elm->isValue()){
                /* @var $elm \Face\Core\EntityFaceElement */
                if($elm->isValue() && !$elm->isPrimary() ){
                    $sets.=",";
                    $sets.=$elm->getSqlColumnName()."=:".$elm->getSqlColumnName();
                }else{
                    if($i>0){
                        $where.=" AND ";
                    }else{
                        $i++;
                    }
                    $where.=$elm->getSqlColumnName()."=:".$elm->getSqlColumnName();
                }

                $this->bindValue(":".$elm->getSqlColumnName(), $this->entity->faceGetter($elm->getName()));
            }


        }
        
        $queryStr= "UPDATE ".$baseFace->getSqlTable()." SET ".ltrim($sets,",")." WHERE ".$where." LIMIT 1";

        return $queryStr;
        
    }

    
}