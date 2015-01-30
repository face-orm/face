<?php

namespace Face\Cache;

use Redis;

class RedisCache extends NamespaceAwareCache{

    /**
     * @var Redis
     */
    protected $redis;

    function __construct(Redis $redis,$namespace = null)
    {
        $this->redis = $redis;
        parent::__construct($namespace);
    }

    protected function _get($key)
    {
        return $this->redis->get($key);
    }

    protected function _delete($key)
    {
        $this->redis->delete($key);
    }

    protected function _set($key, $value)
    {
        $this->redis->set($key, $value);
    }

    protected function _exists($key)
    {
        $this->redis->exists($key);
    }

    protected function _deleteAll($namespace)
    {
        $this->redis->flushDB();
    }


}