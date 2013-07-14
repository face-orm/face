<?php



namespace Face\Core;
/**
 * Description of FaceFactory
 *
 * @author bobito
 */
class FaceFactory {
    
    public static function buildFace($params,$className=null){
        if(is_array($params))
            return new EntityFace($params,$className);
        else if(is_a($params, "Face\Core\FaceInterface"))
            return $params;
        else
            throw new Exception("Invalid type for building a face");
            
    }
    
}

?>
