<?php

class A{
    use \Face\Traits\EntityFaceTrait;

    protected $a;
    protected $b;

    public static function __getEntityFace() {
        return [

            "elements"=>[
                "a"=>[
                    "propertyName"  =>  "a",
                    "type"          =>  "value",
                    "defaultMap"    =>  "a_column",
                    "relation"      =>  "hasOne"
                ],
                "b"=>[
                    "propertyName"  =>  "b",
                    "type"          =>  "entity",
                    "class"         =>  "B",
                    "relation"      =>  "hasOne"
                ]
            ]

        ];
    }
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