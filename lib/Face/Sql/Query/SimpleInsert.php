<?php

namespace Face\Sql\Query;

use Face\Config;
use Face\Core\FaceLoader;
use Face\Util\OOPUtils;

/**
 * Provide a way for face to do inserts ignoring join.
 *
 * @author sghzal
 */
class SimpleInsert extends AbstractModifierSimpleQuery
{
    
    public function getSqlString()
    {
        $baseFace = $this->getBaseFace();
        
        
        $fields="";
        $values="";
        $i=0;
        foreach ($baseFace as $elm) {
            /* @var $elm \Face\Core\EntityFaceElement */
            if ($elm->isValue() && !$elm->getSqlAutoIncrement()) {
                if ($i>0) {
                    $fields.=",";
                    $values.=",";
                } else {
                    $i++;
                }
                $fields.= $elm->getSqlColumnName(true);
                $values.=":".$elm->getSqlColumnName();
                
                $this->bindValue(":".$elm->getSqlColumnName(), $this->entity->faceGetter($elm));
            }
        }
        
        $queryStr= "INSERT INTO ".$baseFace->getSqlTable(true)."($fields) VALUES($values)";
        
        return $queryStr;
        
    }
}
