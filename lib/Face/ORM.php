<?php

namespace Face;

/**
 * Face\ORM is a class that interfaces comon calls of the Face API
 * 
 * This abstract is made to be call everywhere in the application. 
 * An alternative for dependency injection lives at Face\DiOrm
 *
 * @author sghzal
 */
abstract class ORM {
    
    public static function Query($what){
        
        if(is_string($what)){
            $baseFace=  Core\FacePool::getFace($what);
        }else if(is_a("Face\Core\EntityFaceElement")){
            $baseFace=$what;
        }else if(\Peek\Utils\OOPUtils::UsesTrait($what, "Face\Traits\EntityFaceTrait")){
            $baseFace=$what->getEntityFace();
        }else{
            throw new Exception\FacelessException("You asked for a not element that has no face");
        }
        
        return new Sql\Query\FQuery($baseFace);
    }
    
    /**
     * 
     * @param \Face\Sql\Query\FQuery $fQuery
     * @param \PDO $pdo
     * @return Sql\Result\ResultSet
     */
    public static function execute(Sql\Query\FQuery $fQuery, \PDO $pdo){
        $j=$fQuery->execute($pdo);

        $reader=new \Face\Sql\Reader\QueryArrayReader($fQuery);

        $rs=$reader->read($j);
        
        return $rs;
    }
    
}