<?php

class Lemon {

    use \Face\Traits\EntityFaceTrait;

    public $id;
    public $tree_id;
    public $mature;

    public $tree;

    public $seeds=array();

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getTree_id() {
        return $this->tree_id;
    }

    public function setTree_id($tree_id) {
        $this->tree_id = $tree_id;
    }

    public function getMature() {
        return $this->mature;
    }

    public function setMature($mature) {
        $this->mature = $mature;
    }


    public function getTree() {
        return $this->tree;
    }

    public function setTree($tree) {
        $this->tree = $tree;
    }

    public function getSeeds() {
        return $this->seeds;
    }

    public function setSeeds($seeds) {
        $this->seeds = $seeds;
    }






}