<?php



class QueryStringTest extends Test\PHPUnitTestDb
{



    protected function getDataSet()
    {
        return $this->createMySQLXMLDataSet('dataset.xml');
    }

    /**
     * @group querystring
     */
    public function testQueryString(){

        $pdo = $this->getConnection()->getConnection();
        
        $qS = "SELECT tree.id,tree.age,lemon.id as lemon_id,lemon.mature,lemon.tree_id FROM tree JOIN lemon ON lemon.tree_id=tree.id WHERE tree.id = 1";

        $q = new \Face\Sql\Query\QueryString(Tree::getEntityFace(), $qS,
            [
                "this.lemons" => Lemon::getEntityFace()
            ],
            [
                "this.id"=>"id",
                "this.age"=>"age",
                "this.lemons.id"=>"lemon_id",
                "this.lemons.mature"=>"mature",
                "this.lemons.tree_id"=>"tree_id"
            ]
        );
        
        $res = Face\ORM::execute($q, $pdo);
        
        $tree = $res->getInstancesByClass("Tree")[1];
        
        $this->assertEquals(6,count($tree->lemons));
        $this->assertEquals(0,$tree->lemons[4]->mature);
        
        
    }



}
