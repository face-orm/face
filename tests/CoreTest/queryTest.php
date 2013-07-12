<?php

require_once __DIR__.'/../lemonClasses.php';

class queryTest extends PHPUnit_Framework_TestCase
{

    
    public function testGetter()
    {
        
        $pdo = new PDO('mysql:host=localhost;dbname=lemon-test', 'root', 'root');
        

        
        $fQuery=new Face\Sql\Query\FQuery(Lemon::getEntityFace());
        
        $fQuery->join("tree")
               ->join("seeds")
               ->join("tree.leafs")
                ;
               //->where("~a LIKE :name")
               //->bindValue(":name", "%A%");


        
        $j=$fQuery->execute($pdo);
        
        
        var_dump($j->errorInfo());
        $reader=new \Face\Sql\Reader\QueryArrayReader($fQuery);
//        var_dump($reader->read($j));
        $lemons=$reader->read($j)->getInstance("Lemon");
        $trees=$reader->read($j)->getInstance("Tree");
        


        foreach ($trees as $tree){
            echo "tree ".$tree->faceGetidentity().PHP_EOL;
            foreach ($tree->getLemons() as $lemon){
                echo " | lemon ". $lemon->faceGetidentity().PHP_EOL;
                foreach ($lemon->getSeeds() as $seed){
                    echo "   - seed ".$seed->faceGetidentity().PHP_EOL; 
                }
            }
            foreach ($tree->getLeafs() as $leaf){
                echo " | leaf  ". $leaf->faceGetidentity().PHP_EOL;
              
            }
        }
        
        
//        var_dump($j);
        
    }
    

 

}



class At{
    use \Face\Traits\EntityFaceTrait;
    
    protected $id;
    protected $a;
    protected $b;
    
    public static function __getEntityFace() {
        return [
            "sqlTable"=>"a_table",
            "elements"=>[
                
                "id"=>[
                    "propertyName"  =>  "id",
                    "type"          =>  "value",
                    "sql"=>[
                        "columnName"=> "id",
                        "isPrimary"   =>true
                    ]
                ],
                
                "idB"=>[
                    "type"          =>  "value",
                    "sql"=>[
                        "columnName"=> "id_b"
                    ]
                ],
                
                "a"=>[
                    "propertyName"  =>  "a",
                    "type"          =>  "value",
                    "sql"=>[
                        "columnName"=> "a_column"
                    ]
                ],
                "b"=>[
                    "propertyName"  =>  "b",
                    "type"          =>  "entity",
                    "class"         =>  "Bt",
                    "sql"=>[
                        "join"=> ["idB"=>"id"]
                    ]
                    
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

class Bt{
    use \Face\Traits\EntityFaceTrait;
    
    protected $name;
    protected $c;
    
    protected $aParent;
    
    public static function __getEntityFace() {
        return [
            "sqlTable"=>"b_table",
            
            "elements"=>[
                "id"=>[
                    "type"=>"value",
                    "sql"=>[
                        "columnName"=> "id"
                    ]
                ],
                "idC"=>[
                    "type"=>"value",
                    "sql"=>[
                        "columnName"=> "id_c"
                    ]
                ],
                "name"=>[
                    "propertyName"=>"name",
                    "type"=>"value",
                    "sql"=>[
                        "columnName"=> "name"
                    ]
                ],
                "c"=>[
                    "propertyName"=>"c",
                    "type"          =>  "entity",
                    "class"         =>  "Ct",
                    "sql"   =>[
                        "join"  => ["idC"=>"id"]
                    ]
                ],
                "parentA"=>[
                    "propertyName"  =>"aParent",
                    "type"          =>  "entity",
                    "class"         =>  "At",
                    "relation"      =>  "belongsTo",
                    "relatedBy"     =>  "b",
                    "sql"   =>[
                        "join"  => ["id"=>"idB"]
                    ]
                ]
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

class Ct{
    use \Face\Traits\EntityFaceTrait;
    
    protected $name;

    
    public static function __getEntityFace() {
        return [
            
            "sqlTable"=>"c_table",
            
            "elements"=>[
                "id"=>[
                    "type"=>"value",
                    "sql"=>[
                        "columnName"=> "id"
                    ]
                ],
                "name"=>[
                    "propertyName"=>"name",
                    "type"=>"value",
                    "sql"=>[
                        "columnName"=> "c_name"
                    ]
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


?>
