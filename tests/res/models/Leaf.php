<?php

class Leaf implements \Face\Core\EntityInterface {

    public $id;
    public $length;
    public $tree_id;

    use \Face\Traits\EntityFaceTrait;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getLength() {
        return $this->length;
    }

    public function setLength($length) {
        $this->length = $length;
    }

    public function getTree_id() {
        return $this->tree_id;
    }

    public function setTree_id($tree_id) {
        $this->tree_id = $tree_id;
    }


}