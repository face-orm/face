<?php

namespace Face\Sql\Reader;

/**
 * Description of InstancesKeeper
 *
 * @author bobito
 */
class InstancesKeeper {
    
    protected $instances;
    
    public function __construct() {
        $this->instances=array();
    }
    
    public function addInstance($instance,$identity){
        $this->instances[get_class($instance)][$identity]=$instance;
    }
    
    public function hasInstance($className,$identity){
        return isset($this->instances[$className][$identity]);
    }
    
    public function getInstance($className,$identity=null){
        if(null !== $identity)
            return $this->instances[$className][$identity];
        else
            return $this->instances[$className];
    }
    
}

?>
