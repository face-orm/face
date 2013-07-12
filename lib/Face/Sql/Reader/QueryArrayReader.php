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
    
    /**
     * search to put children/parent instance as reference of the given entity
     * @param type $instance
     * @param \Face\Core\EntityFace $face
     * @param type $array
     * @param type $basePath
     * @param type $faceList
     * @throws \Exception
     */
    protected function instanceHyndrateAndForwardEntities($instance,\Face\Core\EntityFace $face,$array,$basePath, $faceList){
        foreach($face as $element){
            if($element->isEntity()){
                
                echo ("ELM : ".$basePath."...".$element->getName());
                echo (" => ".(isset($faceList[$basePath.".".$element->getName()])?"":"NOT ")."join").PHP_EOL;
                if( isset($faceList[$basePath.".".$element->getName()])  ){ // if element is joined
                    
                    $identity = $this->getIdentityOfArray($element->getFace(),$array,$basePath.".".$element->getName());

                    if(!empty($identity)){
                        if ($this->instancesKeeper->hasInstance($element->getClass(), $identity) ){ // if element is already instanciated
                            $childInstance = $this->instancesKeeper->getInstance($element->getClass(), $identity);
                            $instance->faceSetter($element,$childInstance);
                        }else{
                            var_dump($identity);
                            var_dump($this->instancesKeeper);
                            throw new \Exception("TODO : precedence");
                        }
                    }
                }else{

                    $related = \Face\Core\FacePool::getFace( $element->getClass() )->getDirectElement($element->getRelatedBy());
                    
                    if( $related ){

                        
                        /*
                         * A . Look if the child was join by the parent
                         *     
                         *      YES => EASY ! works done... go to the next
                         * 
                         *      NO  => Then it can only work with the parent. Let's check if the parent matches :
                         * 
                         *     
                         * 
                         *          B . $basePath is made of at least 3 element (because we want to check for the parent of the parent
                         *     
                         *          C . Take the parent and look if it is the same class as the child 
                         *              e.g :
                         *                  assuming that "this" is a Lemon.
                         *                  Assuming we have the following paths : 1 this.tree.lemons  and 2 this.tree.leafs
                         *                      then in 1 "lemons" and "this" are both Lemon    
                         *                      then in 2 "leafs" is Leaf but "this" is Lemon => ignore it
                         * 
                         *          D . If same class, we have to make sure that it is the same element. Let's use "related" property for that
                         * 
                         *              YES => Here is the match, fill it now
                         * 
                         *              NO  => Not totaly lost, maybe that there is an implied relation
                         * 
                         *                  E . Look for implied relation
                         *  
                         */
                        
                        
                        
                        // B
                        // this.tree => bad
                        // this.tree.lemon => good
                        if(substr_count($basePath,".")<1){
                            echo "  XX basePath too short, go to the next".PHP_EOL;
                        }else{
                            
                            $relatedBasePath=  \Peek\Utils\StringUtils::subStringBefore($basePath, ".");
                            
                            echo "  Parent path is :$relatedBasePath".PHP_EOL;
                            
                            $parentFace=$faceList[$relatedBasePath];

                            // C
                            // Same class ?
                            echo '    '.$parentFace->getClass()." VS ".$element->getClass();
                            if( $parentFace->getClass() != $element->getClass() ){
                                echo "-".PHP_EOL;
                            }else{
                                echo "+".PHP_EOL;
                                /* @var $parentFace \Face\Core\EntityFace */
                                // D
                                // Look if parent and child refer to the same one
                                if( $parentFace->getDirectElement( $element->getRelatedBy() )->getRelatedBy() == $element->getName() ){
                                
                                    $relatedBasePath=substr($basePath, 0, strrpos( $basePath, '.'));

                                    // if $related is not in $facelist, then it means that $related is not a part of the query, ignore it..
                                    if(isset($faceList[$relatedBasePath.".".$related->getName()])){

                                        $identity = $this->getIdentityOfArray($related->getParentFace(),$array,$relatedBasePath); 


                                        if( $this->instancesKeeper->hasInstance($element->getClass(), $identity) )
                                            $instance->faceSetter($element->getName(), $this->instancesKeeper->getInstance($element->getClass(), $identity) );
                                        else
                                            $this->unfoundPrecedence[]=["instance"=>$instance,"elementToSet"=>$element,"identityOfElement"=>$identity];
                                    }
                                }else{
                                    throw new Exception("TODO ::: LOOK FOR IMPLIED");
                                }
                            }
                            
                        
                            
                        }
                            
                    }

                }
                echo "--".PHP_EOL;
                echo PHP_EOL;
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
