<?php

namespace Face\Sql\Reader;

use \Face\Sql\Query\FQuery;
use Face\Sql\Reader\InstancesKeeper;

/**
 * Description of QueryArrayReader
 *
 * @author bobito
 */
class QueryArrayReader {
    
    /**
     *
     * @var \FaceSql\Query\FQuery
     */
    protected $FQuery;
    /**
     *
     * @var InstancesKeeper
     */
    protected $instancesKeeper;
    
    protected $unfoundPrecedence;
            
    function __construct(\Face\Sql\Query\FQuery $FQuery) {
        $this->FQuery = $FQuery;
        $this->instancesKeeper=new InstancesKeeper();
    }

    
    public function read(\PDOStatement $stmt){
        
        $this->unfoundPrecedence=array();
        
        $faceList = $this->FQuery->getAvailableFaces();
        
        //parsing from the end allows to ensure existance of children when parent are created. Because children are at the end
        $faceList = array_reverse($faceList);
        
        
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            foreach($faceList as $basePath=>$face){
                /* @var $face \Face\Core\EntityFace */
                $identity=$this->getIdentityOfArray($face, $row, $basePath);
                if($this->instancesKeeper->hasInstance($face->getClass(), $identity)){
                    $instance = $this->instancesKeeper->getInstance($face->getClass(), $identity);
                }else{
                    $instance = $this->createInstance($face, $row, $basePath, $faceList);
                    $this->instancesKeeper->addInstance($instance, $identity);
                }
                
                $this->instanceHyndrateAndForwardEntities($instance, $face, $row, $basePath, $faceList);
                
                
                

            }
        }
        
        // set unset instances. To be improved ?
        foreach ($this->unfoundPrecedence as $unfound){
            $unfoundInstance = $this->instancesKeeper->getInstance($unfound['elementToSet']->getClass(), $unfound['identityOfElement']);
            $unfound['instance']->faceSetter($unfound['elementToSet']->getName(),$unfoundInstance);
        }
        
        return $this->instancesKeeper;
        
    }
    
    /**
     * Create an instance from an assoc array  returned by sql
     * @param \Face\Core\EntityFace $face the face that describes the entity
     * @param array $array the array of data
     * @param type $basePath
     * @param type $faceList
     * @return \Face\Sql\Reader\className
     */
    protected function createInstance(\Face\Core\EntityFace $face,$array,$basePath, $faceList){
        

        $className = $face->getClass();
        $instance  = new $className();
        
        foreach($face as $element){
            /* @var $element \Face\Core\EntityFaceElement */

            if($element->isValue()){
                $value=$array[$this->FQuery->_doFQLTableName($basePath.".".$element->getName())];
                $instance->faceSetter($element,$value);
            }
        }
        
        return $instance;
    }
    
    protected function instanceHyndrateAndForwardEntities($instance,\Face\Core\EntityFace $face,$array,$basePath, $faceList){
        foreach($face as $element){
            if($element->isEntity()){
                if( isset($faceList[$basePath.".".$element->getName()])  ){ // if element is joined
                    $identity = $this->getIdentityOfArray($element->getFace(),$array,$basePath.".".$element->getName());




                    if ($this->instancesKeeper->hasInstance($element->getClass(), $identity) ){ // if element is already instanciated
                        $childInstance = $this->instancesKeeper->getInstance($element->getClass(), $identity);
                        $instance->faceSetter($element,$childInstance);
                    }else{
                        throw new Exception("TODO : precedence");
                    }   
                }else{

                    $related = \Face\Core\FacePool::getFace( $element->getClass() )->getDirectElement($element->getRelatedBy());

                    if( $related ){


                        if($element->getRelation()=="belongsTo"){
                            $relatedBasePath=substr($basePath, 0, strrpos( $basePath, '.'));
                        }else
                            $relatedBasePath=$basePath;

                        $identity = $this->getIdentityOfArray($related->getParentFace(),$array,$relatedBasePath); 

                        if($this->instancesKeeper->hasInstance($element->getClass(), $identity))
                            $instance->faceSetter($element->getName(), $this->instancesKeeper->getInstance($element->getClass(), $identity) );
                        else
                            $this->unfoundPrecedence[]=["instance"=>$instance,"elementToSet"=>$element,"identityOfElement"=>$identity];

                    }

                }
            }
        }
    }
  
    
    public function getIdentityOfArray(\Face\Core\EntityFace $face,$array,$basePath){
        $primaries=$face->getPrimaries();
        $identity="";

        foreach($primaries as $elm){
            /* @var $elm \Face\Core\EntityFaceElement */
            
            $identity.=$array[$this->FQuery->_doFQLTableName($basePath.".".$elm->getName())];
        }
        
        return $identity;
    }
    
    
}

?>
