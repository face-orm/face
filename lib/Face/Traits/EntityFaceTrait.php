<?php

namespace Face\Traits;

use \Face\Core\EntityFaceElement;

trait EntityFaceTrait {
    
    
    public function faceGetter($it){
        
        // if $it is an iterator, it means we already parsed the faceQuery
        // else we parse it then we continue
        if(!is_a($it, "\ArrayIterator"))
            $it=new \ArrayIterator(explode(".", $it)); // a face query will be e.g : user.address.name
        
        
        /* @var $element \Face\Core\EntityFaceElement */
        $element=$this->getEntityFace()->getElement($it->current()); // get the element associed with the current string of the iterator
        $value=$this->__faceUseGetter($element); // get the value associed with this object and the element
        
        $it->next(); // we move to next iteration in order to call it on the next value ; only if needed
        
        if($it->valid()){
            return $value->faceGetter($it);
        }else{
            return $value;
        }
    }
    

    
    /**
     * look into the given element to find the way to get the data
     * @param \Face\Core\EntityFaceElement $element the element to get
     * @return mixed
     */
    private function __faceUseGetter(\Face\Core\EntityFaceElement $element){
        
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
    }
    
    
    /**
     * 
     * @return \Face\Core\EntityFace
     */
    public static function getEntityFace(){
        return \Face\Core\FacePool::getFace(__CLASS__);
    }
    
    abstract public static function __getEntityFace();
    
}

?>
