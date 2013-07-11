<?php

namespace Face\Core;

use Peek\Utils\ValuesUtils;

class EntityFaceElement{
    
    /**
     *
     * @var EntityFace
     */
    protected $parentFace;
    
    //entity properties
    protected $name;
    protected $propertyName;
    protected $setter;
    protected $getter;
   
    protected $defaultMap;


    protected $type;
    protected $class;
    
    protected $isIdentifier;
    
    ////////////////////////////
    // relation properties
    /**
     * @var string if it is an entity, it defines what kind of relation : hasOne|belongsTo|hasMany
     */
    protected $relation;
    /**
     * @var string name of the property referencing to this entity on the related entity (e.g helps to define how to set instance reference on the parent)
     */
    protected $relatedBy;
    
    
    
    ////////////////////////////
    // SQL properties
    protected $sqlColumnName;
    protected $sqlIsPrimary;
    protected $sqlJoin;
    protected $sqlBridge;
    

    /**
     * 
     * @param array $params array to construct the faceElement as described here :  TODO array description
     */
    function __construct($name,$params) {
        $this->name         =  $name;
        
        $this->propertyName =  ValuesUtils::getIfArrayKey($params, "propertyName",$name);
        $this->setter       =  ValuesUtils::getIfArrayKey($params, "setter");
        $this->getter       =  ValuesUtils::getIfArrayKey($params, "getter");
        
        $this->defaultMap   =  ValuesUtils::getIfArrayKey($params, "defaultMap");
        
        $this->type         =  ValuesUtils::getIfArrayKey($params, "type","value");
        $this->class        =  ValuesUtils::getIfArrayKey($params, "class");
        $this->isIdentifier =  ValuesUtils::getIfArrayKey($params, "identifier",false);
        
        if($this->isEntity()){
            $this->relation =  ValuesUtils::getIfArrayKey($params, "relation","hasOne");
            $this->relatedBy = ValuesUtils::getIfArrayKey($params, "relatedBy");
        }
            
        if( isset($params['sql']) ){
            $this->sqlColumnName=  ValuesUtils::getIfArrayKey($params['sql'], "columnName",$name);
            $this->sqlIsPrimary =  ValuesUtils::getIfArrayKey($params['sql'], "isPrimary");
            $this->sqlJoin      =  ValuesUtils::getIfArrayKey($params['sql'], "join");
            $this->sqlBridge    =  ValuesUtils::getIfArrayKey($params['sql'], "bridge");
            
        }
       
    }
    
    
    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getPropertyName() {
        return $this->propertyName;
    }

    public function setPropertyName($propertyName) {
        $this->propertyName = $propertyName;
    }

    public function getSetter() {
        return $this->setter;
    }
    
    public function hasSetter(){
        return null !== $this->setter;
    }

    public function setSetter($setter) {
        $this->setter = $setter;
    }

    public function getGetter() {
        return $this->getter;
    }
    
    public function hasGetter(){
        return null !== $this->getter;
    }

    public function setGetter($getter) {
        $this->getter = $getter;
    }

   

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getClass() {
        return $this->class;
    }

    public function setClass($class) {
        $this->class = $class;
    }
    
    public function isEntity(){
        return "entity"===$this->getType();
    }
    
    public function isValue(){
        return "value"===$this->getType();
    }
    
    public function getIsIdentifier() {
        return $this->isIdentifier;
    }
    
    public function isIdentifier() {
        return true===$this->isIdentifier;
    }

    public function setIsIdentifier($isIdentifier) {
        $this->isIdentifier = $isIdentifier;
    }
    
    public function getDefaultMap() {
        return $this->defaultMap;
    }

    
    public function getSqlColumnName() {
        return $this->sqlColumnName;
    }

    public function setSqlColumnName($sqlColumnName) {
        $this->sqlColumnName = $sqlColumnName;
    }

    public function getSqlIsPrimary() {
        return $this->sqlIsPrimary;
    }
    
    public function isPrimary(){
        return $this->sqlIsPrimary;
    }

    public function setSqlIsPrimary($sqlIsPrimary) {
        $this->sqlIsPrimary = $sqlIsPrimary;
    }

    public function getSqlJoin() {
        return $this->sqlJoin;
    }

    public function setSqlJoin($sqlJoin) {
        $this->sqlJoin = $sqlJoin;
    }

    public function getSqlBridge() {
        return $this->sqlBridge;
    }

    public function setSqlBridge($sqlBridge) {
        $this->sqlBridge = $sqlBridge;
    }

    public function getRelation() {
        return $this->relation;
    }

    public function setRelation($relation) {
        $this->relation = $relation;
    }

    public function getRelatedBy() {
        return $this->relatedBy;
    }

    public function setRelatedBy($relatedBy) {
        $this->relatedBy = $relatedBy;
    }

    public function hasRelationTo(){
        
    }        
    
    public function getParentFace() {
        return $this->parentFace;
    }

    public function setParentFace(EntityFace $parentFace) {
        $this->parentFace = $parentFace;
    }

            
    /**
     * 
     * if this is a value type, it means it cant have a face
     * if this is an entity it will return the face matching with the class
     * 
     * @return EntityFace the EntityFace or null
     */
    public function getFace(){
        if($this->isEntity())
            return call_user_func($this->getClass()."::getEntityFace");
        else
            throw new Exception("A value Element has no face. Only entity with an associed class can have a face");
    }

    
   

}

?>
