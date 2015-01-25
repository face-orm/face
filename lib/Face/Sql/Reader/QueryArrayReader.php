<?php

namespace Face\Sql\Reader;

use \Face\Sql\Query\FQuery;
use Face\Core\InstancesKeeper;
use Face\Util\Operation;
use Face\Util\StringUtils;

/**
 * Description of QueryArrayReader
 *
 * @author bobito
 */
class QueryArrayReader implements QueryReaderInterface{

    const OPERATION_PASS=0;
    const OPERATION_FORWARD_JOIN=1;
    const OPERATION_IMPLIED=2;

    /**
     *
     * @var \Face\Sql\Query\FQuery
     */
    protected $FQuery;
    /**
     *
     * @var \Face\Core\InstancesKeeper
     */
    protected $instancesKeeper;

    /**
     *
     * @var \Face\Sql\Result\ResultSet
     */
    protected $resultSet;


    protected $operationsList=array();

    protected $unfoundPrecedence;

    function __construct(\Face\Sql\Query\FQuery $FQuery, InstancesKeeper $instancesKeeper=null) {

        $this->FQuery = $FQuery;

        if(!$instancesKeeper)
            $this->instancesKeeper=new InstancesKeeper();
        else
            $this->instancesKeeper=$instancesKeeper;

        $this->resultSet=new \Face\Sql\Result\ResultSet($this->instancesKeeper);
        
    }


    public function read(\PDOStatement $stmt){

        $this->unfoundPrecedence=array();

        $faceList = $this->FQuery->getAvailableFaces();

        //parsing from the end allows to ensure existence of children when parents are created. Because children are at the end
        $faceList = array_reverse($faceList);


        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {

            // loop over joined faces
            foreach($faceList as $basePath=>$face){
                /* @var $face \Face\Core\EntityFace */
                
                
                
                // get identity of the current face on the current db row
                $identity=$this->_getIdentityOfArray($face, $row, $basePath);

                


                // if already instantiated then get it from ikeeper and try the forwards
                if($this->instancesKeeper->hasInstance($face->getClass(), $identity)){
                    $instance = $this->instancesKeeper->getInstance($face->getClass(), $identity);
                    $this->instanceHydrateAndForwardEntities($instance, $face, $row, $basePath, $faceList, true);

                    if(!$this->resultSet->pathHasIdentity($basePath, $identity)){
                        $this->resultSet->addInstanceByPath($basePath, $instance, $identity);
                    }
                    
                    // else create the instance and hydrate it
                }else{
                    $instance = $this->createInstance($face, $row, $basePath, $faceList);
                    $this->instancesKeeper->addInstance($instance, $identity);
                    $this->resultSet->addInstanceByPath($basePath, $instance, $identity);
                    $this->instanceHydrateAndForwardEntities($instance, $face, $row, $basePath, $faceList, true);
                }





            }


        }

        // set unset instances. To be improved ?
        foreach ($this->unfoundPrecedence as $unfound){
            $unfoundInstance = $this->instancesKeeper->getInstance($unfound['elementToSet']->getClass(), $unfound['identityOfElement']);
            $unfound['instance']->faceSetter($unfound['elementToSet']->getName(),$unfoundInstance);
        }

        return $this->resultSet;

    }

    /**
     * Create an instance from an assoc array  returned by sql
     * @param \Face\Core\EntityFace $face the face that describes the entity
     * @param array $array the array of data
     * @param string $basePath
     * @param array $faceList
     * @return \Face\Sql\Reader\className
     */
    protected function createInstance(\Face\Core\EntityFace $face,$array,$basePath, $faceList){


        $className = $face->getClass();
        $instance  = new $className();

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
    protected function instanceHydrateAndForwardEntities($instance,\Face\Core\EntityFace $face,$array,$basePath, $faceList,$doValues=false){
        foreach($face as $element){
            if($element->isEntity()){

                $pathToElement=$basePath.".".$element->getName();
                
                if( isset($faceList[$pathToElement])  ){ // if element is joined we know what to do

                    $identity = $this->_getIdentityOfArray($element->getFace(),$array,$basePath.".".$element->getName());

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


                    /*
                     * A . Look if the child was join by the parent
                     *
                     *      YES => EASY ! work's done... go to the next
                     *
                     *      NO  => Then it can only work with the parent 
                     *              
                     *              matching case : if current element is this.lemon.tree (where this is the tree) then it works because it refers to this
                     *              no matching case : if current element is this.lemon.seed (where this is the tree) seed doesnt refers to this (tree)
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

                    // A was the previous step



                    // IF NO ACTION WAS ALREADY CALCULATED FOR THIS PATH, THEN DO IT
                    // when action is found, we register it as an operation,
                    // in this way next time we come back to this element, we don't need calculate action again
                    // this is perfect for performances
                    if(!isset($this->operationsList[$pathToElement])){


                        $related = \Face\Core\FacePool::getFace( $element->getClass() )->getDirectElement($element->getRelatedBy());
                        if( $related ){


                            // B
                            // this.tree => bad
                            // this.tree.lemon => good
                            if(substr_count($basePath,".")<1){

                                $this->operationsList[$pathToElement]=new Operation(self::OPERATION_PASS);

                            }else{

                                // find the related base path and take its face
                                $relatedBasePath=  StringUtils::subStringBefore($basePath, ".");
                                $parentFace=$faceList[$relatedBasePath];

                                // C
                                // Same class ?
                                if( $parentFace->getClass() != $element->getClass() ){

                                    $this->operationsList[$pathToElement]=new Operation(self::OPERATION_PASS);

                                }else{

                                    /* @var $parentFace \Face\Core\EntityFace */
                                    // D
                                    // Look if parent and child refer to the same one
                                    if( $parentFace->getDirectElement( $element->getRelatedBy() )->getRelatedBy() == $element->getName() ){

                                        $relatedBasePath=substr($basePath, 0, strrpos( $basePath, '.'));

                                        // if $related is not in $facelist, then it means that $related is not a part of the query, ignore it..
                                        if(isset($faceList[$relatedBasePath.".".$related->getName()])){

                                            $operation = new Operation(self::OPERATION_FORWARD_JOIN);
                                            $operation->setOptions("related",$related);
                                            $operation->setOptions("relatedBasePath",$relatedBasePath);

                                            $this->operationsList[$pathToElement]=$operation;

                                        }else{
                                            $this->operationsList[$pathToElement]=new Operation(self::OPERATION_PASS);
                                        }
                                    }else{
                                        $this->operationsList[$pathToElement]=new Operation(self::OPERATION_IMPLIED);
                                    }

                                }


                            }


                        }else{

                            $this->operationsList[$pathToElement]=new Operation(self::OPERATION_PASS);

                        }
                    }

                    $operation = $this->operationsList[$pathToElement];
                    /* @var $operation Operation */

                    switch($operation->getName()){
                        case self::OPERATION_FORWARD_JOIN :

                            $identity = $this->_getIdentityOfArray($operation->getOptions("related")->getParentFace(),$array,$operation->getOptions("relatedBasePath"));

                            if( $this->instancesKeeper->hasInstance($element->getClass(), $identity) )
                                $instance->faceSetter($element->getName(), $this->instancesKeeper->getInstance($element->getClass(), $identity) );
                            else
                                $this->unfoundPrecedence[]=["instance"=>$instance,"elementToSet"=>$element,"identityOfElement"=>$identity];

                            break;

                        case self::OPERATION_IMPLIED :

                            break;
                    }


                }

            }else if($doValues){
                
                $cName = $this->_makeColumnName($element,$basePath);
                
                
                $value=isset($array[$cName]) ? $array[$cName] : null;
                
                if($value)
                    $instance->faceSetter($element,$value);
            }
        }
    }


    private function _getIdentityOfArray(\Face\Core\EntityFace $face,$array,$basePath){
        $primaries=$face->getPrimaries();
        $identity="";

        foreach($primaries as $elm){
            /* @var $elm \Face\Core\EntityFaceElement */

            $identity.=$array[$this->_makeColumnName($elm,$basePath)];
        }

        return $identity;
    }


    private function _makeColumnName(\Face\Core\EntityFaceElement $elm,$basePath){
        
        $elmName = $elm->getName();
        
        $selectColumns = $this->FQuery->getSelectedColumns();
        if(isset($selectColumns["$basePath.$elmName"])){
            $name = $selectColumns["$basePath.$elmName"];
        }else{
            $name = $this->FQuery->_doFQLTableName($basePath.".".$elmName);
        }
        
        return $name;
        
    }
    
}
