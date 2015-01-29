<?php

namespace Face\Cache;


final class NoCache implements CacheInterface{
    public function get($key){}

    public function delete($key){}

    public function set($key, $value){}

    public function exists($key){}

    public function deleteAll(){}

    protected static $singleInstance;

    public static function singleInstance(){
        if(!static::$singleInstance){
            static::$singleInstance = new static();
        }
        return static::$singleInstance;
    }
}