<?php

namespace Face\Core;

use Peek\StringUtils;

class EntityFace implements \IteratorAggregate, FaceInterface{
    private $elements;
    private $identifiers;
    
    private $class;
    
    /**
     * 
     * @param array $params array to construct the face is described here :  TODO array description
     */
    function __construct($params) {
        $this->elements=array();
        $this->primaries=array();
        

        if(isset($params['elements'])){
            foreach($params['elements'] as $k=>$elmParams){
                $element=new EntityFaceElement($k,$elmParams);
                $this->addElement($element);
            }
        }
    }
    
    public function getClass() {
        return $this->class;
    }

    public function setClass($class) {
        $this->class = $class;
    }

    public function addElement(EntityFaceElement $element){
        $this->elements[$element->getName()]=$element;
        
        
        if($element->isIdentifier())
            $this->identifiers[]=$element;
    }
    

    /**
     *  get the element in this element with the given name
     * @param string $name name of the element to get
     * @param type $offset
     * @param type $pieceOfPath
     * @return EntityFaceElement the EntityFaceElement with the given name
     * @throws \Exception
     * @throws \Face\Exception\RootFaceReachedException
     */
    public function getElement($name,$offset=null,&$pieceOfPath=null){
        
        if(StringUtils::beginsWith("this.", $name))
            $name = substr($name, 5);
        
        if(null!==$offset){
            if($offset<0)
                throw new \Exception("\$offset can't be negativ. ".$offset." given");

            $lastPath="";
            while($offset>0){
                $lastDot= strrpos($name, ".");
                $lastPath=substr($name,$lastDot+1).".".$lastPath;
                $name=substr($name,0,$lastDot);
                $offset--;
            }
            
            
            
            $lastPath=rtrim($lastPath,".");
            $pieceOfPath[0]=$name;
            $pieceOfPath[1]=$lastPath;
            
            if(""===$name){
                throw new \Face\Exception\RootFaceReachedException("Offset was depthly enough to reach root face then it cant get element which references the root Face");
            }
        }
        
        
        if(false!==strpos($name, ".")){
            
            $firstChildFace=$this->getElement(strstr($name, ".",true))->getFace();
            
            return $firstChildFace->getElement(trim(strstr($name, "."),"."));
        }
        
        if(!isset($this->elements[$name]))
            throw new \Exception("Face has no element called '".$name."'");
        
        return $this->elements[$name];
    }
    
    public function getElements() {
        return $this->elements;
    }

    public function setElements($elements) {
        $this->elements = $elements;
    }


    
    public function getIdentifiers(){
        return $this->identifiers;
    }
    

    public function getIterator() {
        return new \ArrayIterator($this->elements);
    }

   

}

?>
