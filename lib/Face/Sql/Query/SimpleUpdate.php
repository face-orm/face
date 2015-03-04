<?php

namespace Face\Sql\Query;

use Face\Config;
use Face\Util\OOPUtils;

/**
 * Provide a way for face to do inserts ignoring join.
 *
 * @author sghzal
 */
class SimpleUpdate extends AbstractModifierSimpleQuery
{
    
    public function getSqlString()
    {
        $baseFace = $this->getBaseFace();
        
        
        $sets="";
        $where="";
        $i=0;
        foreach ($baseFace as $elm) {
            if ($elm->isValue()) {
                /* @var $elm \Face\Core\EntityFaceElement */
                if ($elm->isValue() && !$elm->isPrimary()) {
                    $sets.=",";
                    $sets.= $elm->getSqlColumnName(true).'=:'.$elm->getSqlColumnName();
                } else {
                    if ($i>0) {
                        $where.=" AND ";
                    } else {
                        $i++;
                    }
                    $where.= $elm->getSqlColumnName(true) . '=:'. $elm->getSqlColumnName();
                }

                $this->bindValue(":".$elm->getSqlColumnName(), $this->entity->faceGetter($elm));
            }


        }
        
        $queryStr= "UPDATE ".$baseFace->getSqlTable(true)." SET ".ltrim($sets, ",")." WHERE ".$where." LIMIT 1";

        return $queryStr;
        
    }
}
