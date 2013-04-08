<?php

namespace Face\Core;

use Peek\ValuesUtils;

class Navigator{
    
    
    /**
     *
     * @var \ArrayIterator
     */
    protected $iterator;
    
    /**
     * 
     * @param string $queryString a string query which will be converted to an iterator 
     * which will allow to navigate over an given entity using the trait @see Face\Trait\EntityFaceTrait 
     */
    function __construct($queryString) {
        
        $arrayOfElements=explode(".", $queryString);
        if("this"===$arrayOfElements[0])
            unset($arrayOfElements[0]);
        
        $this->iterator=new \ArrayIterator($arrayOfElements);
        
    }
    
    /**
     * 
     * @param mixed $baseEntity an entity which uses @see Face\Trait\EntityFaceTrait 
     */
    public function chainGet($baseEntity){
        
        
        $this->iterator->rewind();
        
        $entity=$baseEntity;
        
        do{            
            $element = $entity->getEntityFace()->getElement($this->iterator->current());  //get the EntityFaceElement associed with the current string of the iterator
            $entity   = $entity->faceGetter($element);  // get the value of the current element
            
            $this->iterator->next();
            
        }while($this->iterator->valid());
        
        
        return $entity;
        
    }
    
    /**
     * 
     * @param mixed $baseEntity an entity which uses @see Face\Trait\EntityFaceTrait 
     */
    public function chainSet($baseEntity,$value){
        
        $lastElementStr=$this->pop();
        $this->iterator->rewind();

        $entityToUseSetter=$this->chainGet($baseEntity);
        $entityToUseSetter->faceSetter($lastElementStr,$value);
        
        $this->push($lastElementStr);

    }
    
    public function pop(){
        $arrayCopy=$this->iterator->getArrayCopy();
        $popedItem=array_pop($arrayCopy);
        
        $this->iterator=new \ArrayIterator($arrayCopy);
        
        return $popedItem;
    }
    
    public function push($pushedValue){
        $arrayCopy=$this->iterator->getArrayCopy();
        $arrayCopy[]=$pushedValue;
        
        $this->iterator=new \ArrayIterator($arrayCopy);
    }
    
}

?>
