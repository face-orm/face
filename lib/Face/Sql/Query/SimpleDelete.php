<?php

namespace Face\Sql\Query;

use Face\Config;
use Face\Util\OOPUtils;

/**
 * Provide a way for face to do delete ignoring join.
 *
 * @author sghzal
 */
class SimpleDelete extends AbstractModifierSimpleQuery
{
    
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
                    $where.= $elm->getSqlColumnName(true) . '=:' . $elm->getSqlColumnName();
                    $this->bindValue(":" . $elm->getSqlColumnName(), $this->entity->faceGetter($elm));
                }

            }


        }
        
        $queryStr= "DELETE FROM " . $baseFace->getSqlTable(true) . " WHERE ".$where." LIMIT 1";

        return $queryStr;
        
    }
}
