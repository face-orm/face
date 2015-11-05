<?php

namespace Face\Sql\Result;

use Face\Core\FacePool;
use Face\Exception;
use Face\Exception\RootFaceReachedException;
use Face\Sql\Query\SelectBuilder;
use Face\Core\EntityFace;

/**
 * A result set is a list of results returned by a FaceQuery
 *
 * @author sghzal
 */
class ResultSet implements \ArrayAccess, \Countable, \IteratorAggregate
{

    /**
     * @var EntityFace
     */
    protected $baseFace;

    protected $instancesByPathIdentity = [];
    protected $instanceByIndex = [];

    function __construct(EntityFace $baseFace)
    {
        $this->baseFace = $baseFace;
    }

    /**
     * @return EntityFace
     */
    public function getBaseFace()
    {
        return $this->baseFace;
    }

    public function getInstancesByPath($path = null)
    {
        if ($path) {
            return isset($this->instancesByPathIdentity[$path]) ? $this->instancesByPathIdentity[$path] : [];
        } else {
            return $this->instancesByPathIdentity;
        }
    }

    public function setInstances($instancesByPath){
        $this->instancesByPathIdentity = $instancesByPath;
        $this->instanceByIndex = array_values($this->instancesByPathIdentity["this"]);
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
        if (isset($this->instancesByPathIdentity["this"])) {
            return $this->instancesByPathIdentity["this"];
        } else {
            return [];
        }
    }

    /**
     * get the base item a the given index (depends on order by clause).
     * @param int $i Index of the item. It is 0 indexed
     * @return null
     */
    public function getAt($i){
        return isset($this->instanceByIndex[$i])
            ? $this->instanceByIndex[$i]
            : null;
    }




    /*================================
     *  FROM ARRAY ACCESS INTERFACE  =
     *================================*/

    public function offsetExists($offset)
    {
        return isset($this->instanceByIndex[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->instanceByIndex[$offset];
    }

    public function offsetSet($offset, $value)
    {
        throw new Exception("Adding offset to a ResultSet is forbidden");
    }

    public function offsetUnset($offset)
    {
        throw new Exception("removing offset from a ResultSet is forbidden");
    }





    /*=============================
     *  FROM COUNTABLE INTERFACE  =
     *=============================*/

    public function count()
    {
        return isset($this->instancesByPathIdentity["this"]) ? count($this->instancesByPathIdentity["this"]) : 0;
    }



    /*======================================
     *  FROM INTERATOR AGGREGATEINTERFACE  =
     *======================================*/

    public function getIterator()
    {
        return isset($this->instancesByPathIdentity["this"]) ? new \ArrayIterator($this->instancesByPathIdentity["this"]) : new \ArrayIterator();
    }
}
