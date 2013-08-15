<?php

class Leaf {

    public $id;
    public $length;
    public $tree_id;

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

    use \Face\Traits\EntityFaceTrait;

    public static function __getEntityFace() {
        return [
            "sqlTable"=>"leaf",

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
                "length"=>[
                    "type"      => "value",
                    "sql"=>[

                    ]
                ],
                "tree"=>[
                    "class"     =>  "Tree",
                    "relation"  => "belongsTo",
                    "relatedBy" => "leafs",
                    "sql"   =>[
                        "join"  => ["tree_id"=>"id"]
                    ]
                ],


            ]

        ];
    }

}