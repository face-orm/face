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
    
    /**
     * Prepare a query
     * @param string|Core\EntityFaceElement|\Face\Traits\EntityFaceTrait $what it can be a classname, a face object, or an entity that uses EntityFaceTrait
     * @return \Face\Sql\Query\FQuery the query for the entity we asked
     * @throws Exception\FacelessException if the entity we asked doesnt exist
     */
    public static function Query($what){
        
        if(is_string($what)){
            $baseFace=  Core\FacePool::getFace($what);
        }else if(is_a("Face\Core\EntityFaceElement")){
            $baseFace=$what;
        }else if(\Peek\Utils\OOPUtils::UsesTrait($what, "Face\Traits\EntityFaceTrait")){
            $baseFace=$what->getEntityFace();
        }else{
            throw new Exception\FacelessException("You asked a query for something that has no face");
        }
        
        return new Sql\Query\SelectBuilder($baseFace);
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