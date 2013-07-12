<?php

namespace Face\Traits;

use \Face\Core\EntityFaceElement;
use Face\Core\Navigator;

trait EntityFaceTrait {
    
    protected $___faceAlreadySetMany;
    
    /**
     * use the given element and use the right way for getting the value on this instance
     * @param \Face\Core\EntityFaceElement $element the element to get
     * @return mixed
     */
    public function faceGetter($needle){
        
        // look the type of $needle then dispatch
        if( is_string($needle) ){
            /*
             * if it is a string the string can be the name of the element or a chain of elements separated by a dot
             * e.g 
             *  - first form : "elementName"
             *  - secnd form : "elementName1.elementName2.elementName3"
             */
            // TODO catch "this.elementName" case for dont instanciate a Navigator not needed for performances
            if(false!==strpos($needle, "."))
                return (new Navigator($needle))->chainGet($this); // "elementName1.elementName2.elementName3" case
            else
                $element=$this->getEntityFace()->getElement($needle); // "elementName" case
            
        }else if(is_a($needle, "\Face\Core\EntityFaceElement"))
            /*  if is already a face element, dont beed anymore work */
            $element=$needle;
        else
            throw new Exception("Variable of type '".gettype($needle)."' is not a valide type for faceGetter");


        // if has a getter, it can be a custom callable annonymous function, or the name of the the method to call on this object
        if($element->hasGetter()){
            
            $getter = $element->getGetter();
            if(is_string($getter)){ //method of this object
                return $this->$getter();
            }else if(is_callable($getter)){ // custom callable
                return $getter();
            }else{
                throw new Exception('Getter is set but it is not usable : '.var_export($getter,true));
            }
        
        // else we use the property directly
        }else{
            
            $property = $element->getPropertyName();
            return $this->$property;
            
        }
        
        // TODO throw exception on no way to get element
    }
    
    
    
    /**
     * use the given element and use the right way for getting the value on this instance
     * @param \Face\Core\EntityFaceElement $element the element to get
     * @return mixed
     */
    public function faceSetter($path,$value){
        
        // look the type of $needle then dispatch
        if( is_string($path) ){
            /*
             * if it is a string the string can be the name of the element or a chain of elements separated by a dot
             * e.g 
             *  - first form : "elementName"
             *  - secnd form : "elementName1.elementName2.elementName3"
             */
            // TODO catch "this.elementName" case for dont instanciate a Navigator not needed for performances
            
            if(false!==strpos($path, ".")){// "elementName1.elementName2.elementName3" case
                (new Navigator($path))->chainSet($this, $value);
                return $value;
            }else{
                $element=$this->getEntityFace()->getElement($path);
            }
            
        }else if(is_a($path, "\Face\Core\EntityFaceElement")){
            /*  if is already a face element, dont need anymore work */
            $element=$path;
        }else
            throw new Exception("Variable of type '".gettype($path)."' is not a valide type for path of faceSetter");
        
        /* @var $element \Face\Core\EntityFaceElement */
        
        // if has a getter, it can be a custom callable anonymous function, or the name of the the method to call on this object
        if($element->hasSetter()){
            
            $setter = $element->getSetter();
            if(is_string($setter)){ //method of this object
                return $this->$setter($value);
            }else if(is_callable($setter)){ // custom callable
                return $setter($value);
            }else{
                throw new Exception('Setter is set but it is not usable : '.var_export($setter,true));
            }
        
        // else we use the property directly
        }else{
            
            $property = $element->getPropertyName();
            if(!empty($property)){


                if($element->hasManyRelationship()){
                    if(!isset($this->___faceAlreadySetMany[$element->getName()][$value->faceGetidentity()])){
                        
                        if($this->$property ==null)
                            $this->$property=array();
                        
                        array_push($this->$property,$value);
                        $this->___faceAlreadySetMany[$element->getName()][$value->faceGetidentity()]=true;
                    }
                    
                }else{
                    $this->$property=$value;
                }
            }else
                ; // TODO  exception or something else ?
            
        }
        // TODO chainSet in Navigator instead than in this trait
        // TODO throw exception on "no way to set element"

    }
    
    
    
    /**
     * 
     * @return \Face\Core\EntityFace
     */
    public static function getEntityFace(){
        return \Face\Core\FacePool::getFace(__CLASS__);
    }
    
    public function faceHydrate($data,$map){
        foreach($map as $elmName=>$dataName){
            $this->faceSetter($elmName,$data[$dataName]);
        }
    }
    
    
    public function faceGetidentity(){
        $array=self::getEntityFace()->getIdentifiers();
        
        
        $identityString="";
        
        foreach($array as $element){
            $identityString.=$this->faceGetter($element);
        }
        
        return $identityString;
        
    } 
    
    /**
     * Takes the DefaultMap contained in this face to create a default map.
     * The given array (the map) maps the element name (the key) to a string (which most of time represnts the sql column name).
     * Each line of the array has this form : ElementName => Wished name
     * @param array $exclude Elements to exclude. e.g. : if you want to exlucde elements "id" and "name" just use ["id","name"]
     * @param array $include Elements to include or replace. e.g : if "name" has not default it wont be included if you dont specify it in the map. Just use ["name"=>"name"]
     * @return array The map with the form [ElementName => Wished name]
     */
    public static function faceDefaultMap($exclude=[],$include=[]){
        $map=array();
        $face=self::getEntityFace();

        foreach($face as $elm){
            /* @var $elm EntityFaceElement */
            if(!in_array($elm->getName(), $exclude) && $elm->getDefaultMap())
                $map[$elm->getName()]=$elm->getDefaultMap();
        }
        
        foreach($include as $name=>$mapName){
            $map[$name]=$mapName;
        }

        return $map;
    }
    
    
    abstract public static function __getEntityFace();
    
}

?>
