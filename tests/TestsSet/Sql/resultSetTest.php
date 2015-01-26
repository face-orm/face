<?php

class resultSetTest extends Test\PHPUnitTestDb
{

    
    public function testResultSetJoin()
    {


        $pdo = $this->getConnection()->getConnection();
        
        // expected data
        $fQuery = Tree::faceQueryBuilder();
        $fQuery->join("lemons")->join("lemons.seeds")->join("leafs");
        $trees1 = Face\ORM::execute($fQuery, $pdo);


        // tested data
        $fQuery = Tree::faceQueryBuilder();
        $fQuery->join("lemons")->join("lemons.seeds");
        $trees2 = Face\ORM::execute($fQuery, $pdo);

        $fQuery = Leaf::faceQueryBuilder();
//        $fQuery->whereINRelation(
//            "tree",$trees2->getInstancesByPath("this")
//        );


        //$this->assertEquals($trees1,$trees2);

        
    }
}
