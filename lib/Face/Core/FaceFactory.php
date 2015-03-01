<?php



namespace Face\Core;

/**
 * Description of FaceFactory
 *
 * @author bobito
 */
class FaceFactory
{
    
    public static function buildFace($params,FaceLoader $faceLoader = null)
    {
        if (is_array($params)) {
            return new EntityFace($params, $faceLoader);
        } elseif ($params instanceof \Face\Core\EntityFace) {
            return $params;
        } else {
            throw new \Exception("Invalid type for building a face");
        }
            
    }
}
