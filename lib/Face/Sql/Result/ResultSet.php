<?php

namespace Face\Sql\Result;

/**
 * A result set is a list of results returned by a FaceQuery
 *
 * @author sghzal
 */
class ResultSet implements \ArrayAccess,\Countable, \IteratorAggregate {
    
    /**
     *
     * @var \Face\Core\InstancesKeeper
     */
    protected $instanceKeeper;
    
    protected $instancesByPath=array();
    
    function __construct(\Face\Core\InstancesKeeper $instanceKeeper) {
        $this->instanceKeeper = $instanceKeeper;
    }

    public function getInstanceKeeper() {
        return $this->instanceKeeper;
    }

    public function getInstancesByPath($path=null) {
        if($path)
            return $this->instancesByPath[$path];
        else
            return $this->instancesByPath;
    }

    public function getInstancesByClass($className) {
        return $this->instanceKeeper->getInstance($className);
    }
    

    public function addInstanceByPath($path,$instance) {
        $this->instancesByPath[$path][] = $instance;
    }

    
    
    public function getBaseInstances() {
        return $this->instancesByPath["this"];
    }

 
    
    
    
    
    /*================================
     *  FROM ARRAY ACCESS INTERFACE  =
     *================================*/
    
    public function offsetExists($offset) {
        return isset($this->instancesByPath["this"][$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->instancesByPath["this"][$offset]) ? $this->instancesByPath["this"][$offset] : null;
    }

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->$this->instancesByPath["this"][]        = $value;
        } else {
            $this->$this->instancesByPath["this"][$offset] = $value;
        }
    }

    public function offsetUnset($offset) {
        unset($this->$this->instancesByPath["this"][$offset]);
    }

    
    
    
  
    /*=============================
     *  FROM COUNTABLE INTERFACE  =
     *=============================*/
    
    public function count() {
        return isset($this->instancesByPath["this"]) ? count($this->instancesByPath["this"]) : 0;
    }

    
    
    /*======================================
     *  FROM INTERATOR AGGREGATEINTERFACE  =
     *======================================*/
    
    public function getIterator() {
        return isset($this->instancesByPath["this"]) ? new \ArrayIterator($this->instancesByPath["this"]) : new \ArrayIterator();
    }

  
}