<?php

namespace Face\Core;

use Face\Util\OOPUtils;

/**
 * This is an internal core class, it should be used only internally by the library
 */
abstract class FacePool implements \IteratorAggregate{
    

    private static $faces=array();
    
    

    /**
     * 
     * @param type $className
     * @return EntityFace
     * @throws Exception
     */
    public static function getFace($className){
        if(!isset(self::$faces[$className])){   // if the class is not in the pool then register it
            
            if(OOPUtils::UsesTrait($className, "Face\Traits\EntityFaceTrait")){    // using the trait is needed. If no let's throw an exception.
                self::$faces[$className]= FaceFactory::buildFace(call_user_func($className."::__getEntityFace") , $className );
            }else
                throw new \Face\Exception\FacelessException("The class ".$className." doesn't use the trait Face\Traits\EntityFaceTrait");
        }

        return self::$faces[$className];
    }
}
