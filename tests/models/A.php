<?php

class A{
    use \Face\Traits\EntityFaceTrait;

    protected $a;
    protected $b;

    public function getA() {
        return $this->a;
    }

    public function setA($a) {
        $this->a = $a;
    }

    public function getB() {
        return $this->b;
    }

    public function setB($b) {
        $this->b = $b;
    }


}