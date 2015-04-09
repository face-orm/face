<?php

namespace Face\Sql\Result;

use Face\Core\FacePool;
use Face\Exception\RootFaceReachedException;
use Face\Sql\Query\SelectBuilder;
use Face\Core\EntityFace;
use Face\Core\InstancesKeeper;

/**
 * A result set is a list of results returned by a FaceQuery
 *
 * @author sghzal
 */
class ResultSet implements \ArrayAccess, \Countable, \IteratorAggregate
{
    
    /**
     *
     * @var \Face\Core\InstancesKeeper
     */
    protected $instanceKeeper;

    /**
     * @var EntityFace
     */
    protected $baseFace;
    
    protected $instancesByPath=array();
    protected $instancesByPathIdentity=array();
    
    function __construct(EntityFace $baseFace, InstancesKeeper $instanceKeeper)
    {
        $this->instanceKeeper = $instanceKeeper;
        $this->baseFace = $baseFace;
    }

    public function getInstanceKeeper()
    {
        return $this->instanceKeeper;
    }

    public function getInstancesByPath($path = null)
    {
        if ($path) {
            return isset($this->instancesByPath[$path]) ? $this->instancesByPath[$path] : [];
        } else {
            return $this->instancesByPath;
        }
    }

    public function getInstancesByClass($className)
    {
        return $this->instanceKeeper->getInstance($className);
    }
    

    public function addInstanceByPath($path, $instance, $identity)
    {
        $this->instancesByPath[$path][] = $instance;
        $this->instancesByPathIdentity[$path][$identity] = $instance;
    }
    
    public function pathHasIdentity($path, $identity)
    {
        return isset($this->instancesByPathIdentity[$path][$identity]);
    }

    /**
     * @return array list of existing pathes
     */
    public function getPathes(){
        return array_keys($this->instancesByPathIdentity);
    }
    
    public function getBaseInstances()
    {
        if (isset($this->instancesByPath["this"])) {
            return $this->instancesByPath["this"];
        } else {
            return [];
        }
    }

    public function first(){
        return isset($this[0]) ? $this[0] : null;
    }

    /**
     * get the base item a the given index (depends on order by clause).
     * @param int $i Index of the item. It is 0 indexed
     * @return null
     */
    public function getAt($i){
        return isset($this[$i]) ? $this[$i] : null;
    }

    /**
     * @param EntityFace $join
     * @param EntityFace $from
     * @param \PDO $PDO
     */
//    public function queryJoin($what, \PDO $PDO){
//
//
//        // TODO
//
//        $join = $this->baseFace->getElement($what);
//
//        try {
//            $from = $this->baseFace->getElement($what, 1, $pieces);
//            $joinPath = $pieces[1];
//        } catch (RootFaceReachedException $e) {
//            $from = $this->baseFace;
//            $joinPath = $what;
//        }
//
//        $instances = $this->getInstancesByClass($from->getClass());
//
//        if(!$instances){
//            return;
//        }else{
//            $query = new SelectBuilder($from->getFace());
//            $query->join($joinPath);
//
//            $from->getFace()->getIdentifiers();
//        }
//
//
//
//    }
    
    
    
    
    /*================================
     *  FROM ARRAY ACCESS INTERFACE  =
     *================================*/
    
    public function offsetExists($offset)
    {
        return isset($this->instancesByPath["this"][$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->instancesByPath["this"][$offset]) ? $this->instancesByPath["this"][$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->$this->instancesByPath["this"][]        = $value;
        } else {
            $this->$this->instancesByPath["this"][$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->$this->instancesByPath["this"][$offset]);
    }

    
    
    
  
    /*=============================
     *  FROM COUNTABLE INTERFACE  =
     *=============================*/
    
    public function count()
    {
        return isset($this->instancesByPath["this"]) ? count($this->instancesByPath["this"]) : 0;
    }

    
    
    /*======================================
     *  FROM INTERATOR AGGREGATEINTERFACE  =
     *======================================*/
    
    public function getIterator()
    {
        return isset($this->instancesByPath["this"]) ? new \ArrayIterator($this->instancesByPath["this"]) : new \ArrayIterator();
    }
}
