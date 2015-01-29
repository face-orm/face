<?php

class Seed{
    public $id;
    public $lemon_id;
    public $fertil;

    public $lemon;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getLemon_id() {
        return $this->lemon_id;
    }

    public function setLemon_id($lemon_id) {
        $this->lemon_id = $lemon_id;
    }

    public function getFertil() {
        return $this->fertil;
    }

    public function setFertil($fertil) {
        $this->fertil = $fertil;
    }

    public function getLemon() {
        return $this->lemon;
    }

    public function setLemon($lemon) {
        $this->lemon = $lemon;
    }


    use \Face\Traits\EntityFaceTrait;




}