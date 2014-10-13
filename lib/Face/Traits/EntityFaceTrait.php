<?php

namespace Face\Traits;

use \Face\Core\EntityFaceElement;
use Face\Core\Navigator;
use Face\ORM;
use Face\Sql\Query\SelectBuilder;

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


                if($element->hasManyRelationship() || $element->hasManyThroughRelationship() ){
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

        if(!$array || 0==count($array))
            throw new \Exception("The Class ".__CLASS__." has no face identifier.");

        
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
    
    /**
     * Shortcut to construct a FQuery
     * @return SelectBuilder
     */
    public static function faceQueryBuilder(){
        return new SelectBuilder(self::getEntityFace());
    }

    /**
     * Fast querying by only specifying one field
     * @param $item string name of the item (without the leading tild) e.g : "id"
     * @param $itemValue string|array value to find. You may specify an array to use an WHERE item IN (...) instead of WHERE item = ...
     * @param $pdo
     * @return \Face\Sql\Result\ResultSet
     */
    public static function faceQueryBy($item,$itemValue,$pdo){
            if(self::getEntityFace()->getDirectElement($item)){
                $fQuery = self::faceQueryBuilder();

                if(is_array($itemValue))
                    $fQuery->whereIN("~$item",$itemValue);
                else
                    $fQuery->where("~$item=:itemValue")
                           ->bindValue(":itemValue",$itemValue);

                return ORM::execute($fQuery,$pdo);

            }
    }
    
    public static function __getEntityFace(){
        throw new \Exception("__getEntityFace Method must be overwritten");
    }

    /**
     * 
     * @param string $string the SQL query 
     * @param type $options
     */
    public static function queryString($string,$options){
        
        $joins = [];
        $selectedColumns = [];
        
        if(is_array($options)){
            
            if(isset($options["join"])){
                
                
                
                foreach ($options["join"] as $j){
                    
                    $joins["this.$j"] = self::getEntityFace()->getElement($j)->getFace();
                }
            }
            
            if(isset($options["select"])){
                foreach ($options["select"] as $k=>$j){
                    
                    $basePath = is_numeric($k) ? $j : $k;
                    
                    if(!\Peek\Utils\StringUtils::beginsWith("this", $basePath)){
                        $basePath = "this." . $basePath;
                    }
                    
                    $basePath = substr($basePath,0, strrpos( $basePath, "."));
                    
                    self::__queryStringDoColumnRecursive($selectedColumns,$k,$j,$basePath);

                    
                }
            }
            
        }        
        $qS = new \Face\Sql\Query\QueryString(self::getEntityFace(), $string, $joins, $selectedColumns);
        
        return $qS;
        
    }
    
    private static function __queryStringDoColumnRecursive(&$selectedColumns,$k,$v,$basePath){
       
        
        // case   "lemons.tree_id"
        if(is_numeric($k)){
                        
            $pos = strrpos( $v, ".");
            $columnName = false ===  $pos ? $v : substr($v, $pos+1);
            $selectedColumns["$basePath.$columnName"] = $columnName;

        }else{

            // case  "lemons" => [ "tree_id" ]  
            if(is_array($v)){

                foreach ($v as $kk=>$vv ){
                    $newBasePath = "$basePath.$k." . (is_numeric($kk) ? $vv : $kk);
                    $newBasePath = substr($newBasePath,0, strrpos( $newBasePath, "."));
                    self::__queryStringDoColumnRecursive($selectedColumns, $kk, $vv, $newBasePath );
                    
                }
               
            // case  "lemons.tree_id"=>"tree_id"
            }else{
                $pos = strrpos( $k, ".");
                $nameEnd = false ===  $pos ? $k : substr($k, $pos+1);
                $selectedColumns["$basePath.$nameEnd"] = $v;
            }

        }

        
    }
    
}
