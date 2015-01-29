<?php

class B{
    use \Face\Traits\EntityFaceTrait;

    protected $name;



    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }





}
