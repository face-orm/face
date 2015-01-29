<?php

namespace Face\Cache;

interface CacheInterface {

    public function get($key);

    public function delete($key);

    public function set($key, $value);

    public function exists($key);

    public function deleteAll();

}