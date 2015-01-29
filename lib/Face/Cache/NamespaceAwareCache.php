<?php

namespace Face\Cache;


abstract class NamespaceAwareCache implements CacheInterface {


    protected $namespace = null;
    protected $namespaceSeparator = ".";

    function __construct($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * concat the namespace and the key to get a namespaced key
     * @param $key
     * @return string
     */
    public function useNamespace($key){

        if(null == $this->namespace){
            return $key;
        }

        return $this->namespace . $this->namespaceSeparator . $key;

    }

    /**
     * @return string the namespace of the class
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @inheritdoc
     */
    public function get($key)
    {
        return $this->_get($this->useNamespace($key));
    }

    /**
     * @inheritdoc
     */
    public function delete($key)
    {
        $this->_delete($this->useNamespace($key));
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value)
    {
        $this->_set($this->useNamespace($key), $value);
    }

    /**
     * @inheritdoc
     */
    public function exists($key)
    {
        $this->_exists($this->useNamespace($key));
    }

    public function deleteAll(){
        $this->_deleteAll($this->getNamespace());
    }



    abstract protected function _get($key);

    abstract protected function _delete($key);

    abstract protected function _set($key, $value);

    abstract protected function _exists($key);

    abstract protected function _deleteAll($namespace);


}