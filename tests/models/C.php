<?php


class C{
    use \Face\Traits\EntityFaceTrait;

    protected $name;


    public static function __getEntityFace() {
        return [

            "elements"=>[
                "name"=>[
                    "propertyName"=>"name",
                    "type"=>"value",
                ],
            ]

        ];
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }





}