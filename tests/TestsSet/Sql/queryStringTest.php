<?php



class QueryStringTest extends Test\PHPUnitTestDb
{


    
    
    
    CONST QUERY_STRING = 
           "SELECT tree.id,tree.age,lemon.id as lemon_id,lemon.mature,lemon.tree_id, seed.id as seed_id
              FROM tree 
              JOIN lemon ON lemon.tree_id=tree.id 
              LEFT JOIN seed ON seed.lemon_id=lemon.id 
              WHERE tree.id = 1"
        ;
    
    private function _testAssertion($tree){
        
        $this->assertEquals(6,count($tree->lemons));
        $this->assertEquals(0,$tree->lemons[4]->mature);
        $this->assertEquals(8,$tree->age);
        $this->assertEquals(3,count($tree->lemons[0]->seeds));
        
    }
    
    

    /**
     * @group querystring
     */
    public function testQueryString(){
    

        $pdo = $this->getConnection()->getConnection();
        
        $qS = self::QUERY_STRING;

        $q = new \Face\Sql\Query\QueryString(Tree::getEntityFace(), $qS,
            [
                "this.lemons" => Lemon::getEntityFace(),
                "this.lemons.seeds" => Seed::getEntityFace()
            ],
            [
                "this.id"=>"id",
                "this.age"=>"age",
                "this.lemons.id"=>"lemon_id",
                "this.lemons.mature"=>"mature",
                "this.lemons.tree_id"=>"tree_id",
                "this.lemons.seeds.id"=>"seed_id",
                "this.lemons.seeds.lemon_id"=>"lemon_id",
                "this.lemons.seeds.fertil"=>"fertil",
            ]
        );
        
        $res = Face\ORM::execute($q, $pdo);
        
        $tree = $res->getInstancesByClass("Tree")[1];
        
        $this->_testAssertion($tree);
       
        
        
    }
    
    /**
     * @group querystring
     */
    public function testQueryStringTraitGenerator(){
        
        $pdo = $this->getConnection()->getConnection();
        
        $q = Tree::queryString(self::QUERY_STRING, [
            
            "join"   => ["lemons","lemons.seeds"],
            "select" => [
                
                // TREE
                "id","this.age",
                
                // LEMON
                "lemons.id"=>"lemon_id",
                "lemons.mature",
                "lemons" => [
                    "tree_id",
                    "seeds.id"=>"seed_id",
                    "seeds"=>[
                        "lemon_id",
                    ]
                ],
                "lemons.seeds.fertil"
                
            ]
            
        ]);
        
        $res = Face\ORM::execute($q, $pdo);
        
        $tree = $res->getInstancesByClass("Tree")[1];
        
        $this->_testAssertion($tree);
        
        
    }



}
