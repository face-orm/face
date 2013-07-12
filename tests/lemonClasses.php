<?php


class Tree {

    use \Face\Traits\EntityFaceTrait;
    
    public $id;
    public $age;
    public $lemons=array();
    public $leafs=array();
    
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
                    "type"=>"value",
                    "identifier"=>true,
                    "sql"=>[
                        "columnName"=> "id",
                        "isPrimary" => true
                    ]
                ],
                "age"=>[
                    "type"      => "value",
                    "sql"=>[
                        "columnName" => "age"
                    ]
                ],
                "lemons"=>[
                    "type"      => "entity",
                    "class"     => "Lemon",
                    "relation"  => "hasMany",
                    "relatedBy" => "tree",
                    "sql"   =>[
                        "join"  => ["id"=>"tree_id"]
                    ]
                ],
                "leafs"=>[
                    "type"      => "entity",
                    "class"     => "Leaf",
                    "relation"  => "hasMany",
                    "relatedBy" => "tree",
                    "sql"   =>[
                        "join"  => ["id"=>"tree_id"]
                    ]
                ]
              
            ]
            
        ];
    }
    
}

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
                    "type"      => "entity",
                    "class"     =>  "Tree",
                    "relation"  => "belongsTo",
                    "relatedBy" => "lemons",
                    "sql"   =>[
                        "join"  => ["tree_id"=>"id"]
                    ]
                ],
                "seeds"=>[
                    "type"      => "entity",
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
                    "type"      => "entity",
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

    public static function __getEntityFace() {
        return [
            "sqlTable"=>"seed",
            
            "elements"=>[
                "id"=>[
                    "type"=>"value",
                    "identifier"=>true,
                    "sql"=>[

                        "isPrimary" => true
                    ]
                ],
                "lemon_id"=>[
                    "type"      => "value",
                    "sql"=>[

                    ]
                ],
                "fertil"=>[
                    "type"      => "value",
                    "sql"=>[

                    ]
                ],
                "lemon"=>[
                    "type"      => "entity",
                    "class"     =>  "Lemon",
                    "relation"  => "belongsTo",
                    "relatedBy" => "seeds",
                    "sql"   =>[
                        "join"  => ["lemon_id"=>"id"]
                    ]
                ]
              
            ]
            
        ];
    }


    
}
