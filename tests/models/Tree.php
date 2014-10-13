<?php

class Tree {

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



    public static function __getEntityFace() {
        return [
            "sqlTable"=>"tree",

            "elements"=>[
                "id"=>[
                    "identifier"=>true,
                    "sql"=>[
                        "columnName"=> "id",
                        "isPrimary" => true
                    ]
                ],
                "age",
                "lemons"=>[
                    "class"     => "Lemon",
                    "relation"  => "hasMany",
                    "relatedBy" => "tree",
                    "sql"   =>[
                        "join"  => ["id"=>"tree_id"]
                    ]
                ],
                "leafs"=>[
                    "class"     => "Leaf",
                    "relation"  => "hasMany",
                    "relatedBy" => "tree",
                    "sql"   =>[
                        "join"  => ["id"=>"tree_id"]
                    ]
                ],
                
                "childrenTrees"=>[
                    "class"     => "Tree",
                    "relation"  => "hasManyThrough",
                    "relatedBy" => "parentTrees",
                    "sql"   =>[
                        "join"  => ["id"=>"tree_parent_id"],
                        "throughTable" => "tree_has_parent"
                    ]
                ],
                
                "parentTrees"=>[
                    "class"     => "Tree",
                    "relation"  => "hasManyThrough",
                    "relatedBy" => "childrenTrees",
                    "sql"   =>[
                        "join"  => ["id"=>"tree_child_id"],
                        "throughTable" => "tree_has_parent"
                    ]
                ]
                

            ]

        ];
    }

}