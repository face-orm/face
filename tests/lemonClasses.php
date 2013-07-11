<?php


class Tree {

    use \Face\Traits\EntityFaceTrait;
    
    public $id;
    public $age;
    public $lemons=array();
    
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
                ]
              
            ]
            
        ];
    }

    
    
    
}


class leaf {

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
}

class seed{
    public $id;
    public $lemon_id;
    public $fertil;
    
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


    
}
