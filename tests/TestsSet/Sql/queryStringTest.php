<?php

use Face\Sql\Query\SelectBuilder\JoinQueryFace;
use Face\Sql\Query\SelectBuilder\QueryFace;


class QueryStringTest extends Test\PHPUnitTestDb
{





    CONST QUERY_STRING =
           "SELECT tree.id, tree.age, lemon.id as lemon_id,lemon.mature,lemon.tree_id, seed.id as seed_id
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

        $this->markTestSkipped("Query string should be derivable to a SelectInterface");

        $pdo = $this->getConnection()->getConnection();

        $qS = self::QUERY_STRING;

        $q = new \Face\Sql\Query\QueryString(Tree::getEntityFace(), $qS);
        $q->getBaseQueryFace()->setColumns(["age" => "age", "id" => "id"]);
        $q->setJoin("lemons", ["tree_id" => "tree_id",  "mature" => "mature", "id" => "lemon_id"]);
        $q->setJoin("lemons.seeds", ["id" => "seed_id"]);

        $sql = $pdo->prepare($q->getSqlString());
        $sql->execute();


        $res = Face\ORM::execute($q, $pdo);

        $tree = $res->getAt(0);

        $this->_testAssertion($tree);

    }

    /**
     * @group querystring
     */
    public function testQueryStringTraitGenerator(){

        $this->markTestSkipped("Query string should be derivable to a SelectInterface");

        $pdo = $this->getConnection()->getConnection();

        $q = Tree::queryString(self::QUERY_STRING);
        $q->getBaseQueryFace()->setColumns(["age" => "age", "id" => "id"]);
        $q->setJoin("lemons", ["tree_id" => "tree_id",  "mature" => "mature", "id" => "lemon_id"]);
        $q->setJoin("lemons.seeds", ["id" => "seed_id"]);

        $res = Face\ORM::execute($q, $pdo);

        $tree = $res->getAt(0);

        $this->_testAssertion($tree);


    }


    public function testBindIn(){

        $pdo = $this->getConnection()->getConnection();

        $q = Tree::queryString("SELECT id FROM lemon WHERE lemon.id IN (::lemonIds)");
        $q->bindIn("::lemonIds", [1,2,4]);

        $q->getBoundValues();

        $expected = "SELECT id FROM lemon WHERE lemon.id IN (:fautoIn1,:fautoIn2,:fautoIn3)";

        $this->assertEquals($expected, $q->getSqlString());
        $this->assertEquals(1, $q->getBoundValue(":fautoIn1")[0]);
        $this->assertEquals(2, $q->getBoundValue(":fautoIn2")[0]);
        $this->assertEquals(4, $q->getBoundValue(":fautoIn3")[0]);

    }



}
