<?php

class Tree implements \Face\Core\EntityInterface {

    use \Face\Traits\EntityFaceTrait;

    public $id;
    public $age;
    public $lemons=array();
    public $leafs=array();
    public $childrenTrees=array();
    public $parentTrees = array();
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getAge() {
        return $this->age;
    }

    public function setAge($age) {
        $this->age = $age;
    }

    public function getLemons() {
        return $this->lemons;
    }

    public function setLemons($lemons) {
        $this->lemons = $lemons;
        echo "TREE : ".$this->id." SET LEMON : ".$lemons->getId().PHP_EOL;
    }




    public function getLeafs() {
        return $this->leafs;
    }

    public function setLeafs($leafs) {
        $this->leafs = $leafs;
    }


}