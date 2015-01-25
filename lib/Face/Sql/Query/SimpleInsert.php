<?php

namespace Face\Sql\Query;

use Face\Util\OOPUtils;

/**
 * Provide a way for face to do inserts ignoring join. 
 *
 * @author sghzal
 */
class SimpleInsert extends FQuery {
 
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
        
        
        $fields="";
        $values="";
        $i=0;
        foreach ($baseFace as $elm){
            /* @var $elm \Face\Core\EntityFaceElement */
            if($elm->isValue() && !$elm->getSqlAutoIncrement() ){
                if($i>0){
                    $fields.=",";
                    $values.=",";
                }else{
                    $i++;
                }
                $fields.="`" . $elm->getSqlColumnName() . "`";
                $values.=":".$elm->getSqlColumnName();
                
                $this->bindValue(":".$elm->getSqlColumnName(), $this->entity->faceGetter($elm->getName()));
            }
        }
        
        $queryStr= "INSERT INTO `".$baseFace->getSqlTable()."`($fields) VALUES($values)";
        
        return $queryStr;
        
    }

    
}