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




    public static function __getEntityFace() {
        return [
            "sqlTable"=>"lemon",

            "elements"=>[
                "id"=>[
                    "type"=>"value",
                    "identifier"=>true,
                    "sql"=>[
                        "columnName"=> "id",
                        "isPrimary" => true
                    ]
                ],
                "tree_id"=>[
                    "type"      => "value",
                    "sql"=>[
                        "columnName" => "tree_id"
                    ]
                ],
                "mature"=>[
                    "type"      => "value",
                    "sql"=>[
                        "columnName" => "mature"
                    ]
                ],
                "tree"=>[
                    "class"     =>  "Tree",
                    "relatedBy" => "lemons",
                    "relation"  => "belongsTo",
                    "sql"   =>[
                        "join"  => ["tree_id"=>"id"]
                    ]
                ],
                "seeds"=>[
                    "class"     => "Seed",
                    "relation"  => "hasMany",
                    "relatedBy" => "lemon",
                    "sql"   =>[
                        "join"  => ["id"=>"lemon_id"]
                    ]
                ]

            ]

        ];
    }




}