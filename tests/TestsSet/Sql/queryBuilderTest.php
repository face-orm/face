<?php


class queryBuilderTest extends Test\PHPUnitTestDb
{



    public function testSimpleSelect()
    {

        $pdo=$this->getConnection()->getConnection();

        $q=Tree::faceQueryBuilder();
        $trees=\Face\ORM::execute($q,$pdo);

        $this->assertEquals(4,count($trees));

        $this->assertEquals(array(1,8),array( $trees[0]->getId() , $trees[0]->getAge() ));
        $this->assertEquals(array(2,2),array( $trees[1]->getId() , $trees[1]->getAge() ));
        $this->assertEquals(array(3,5),array( $trees[2]->getId() , $trees[2]->getAge() ));
        $this->assertEquals(array(4,300),array( $trees[3]->getId() , $trees[3]->getAge() ));



        // test whereIn()
        $q=Tree::faceQueryBuilder()->whereIN("~id",array(1,2));
        $trees=\Face\ORM::execute($q,$pdo);
        $this->assertEquals(2,count($trees));
        $this->assertEquals(array(1,8),array( $trees[0]->getId() , $trees[0]->getAge() ));
        $this->assertEquals(array(2,2),array( $trees[1]->getId() , $trees[1]->getAge() ));

        $q=Tree::faceQueryBuilder()->whereIN("~age",array(300,2,5))->whereIN("~id",array(2,3));
        $trees=\Face\ORM::execute($q,$pdo);
        $this->assertEquals(2,count($trees));
        $this->assertEquals(array(2,2),array( $trees[0]->getId() , $trees[0]->getAge() ));
        $this->assertEquals(array(3,5),array( $trees[1]->getId() , $trees[1]->getAge() ));


        $trees = Tree::faceQueryBy("id",1,$pdo);
        $this->assertEquals(1,count($trees));
        $this->assertEquals(array(1,8),array( $trees[0]->getId() , $trees[0]->getAge() ));

        $trees = Tree::faceQueryBy("id",array("1,2"),$pdo);
        $this->assertEquals(1,count($trees));
        $this->assertEquals(array(1,8),array( $trees[0]->getId() , $trees[0]->getAge() ));


    }

    public function testLimitAndOffset(){

        $q=Tree::faceQueryBuilder();
        $q->setLimit(2);
        $q->setOffset(1);

        $sqlString = $q->getSqlString();

        $this->assertEquals("SELECT `this`.`id` AS `this.id`,`this`.`age` AS `this.age` FROM `tree` AS `this` LIMIT 2 OFFSET 1", $sqlString);



        $pdo=$this->getConnection()->getConnection();
        $trees=\Face\ORM::execute($q,$pdo);
        $this->assertEquals(2, $trees->count());
        $this->assertEquals(2, $trees->getAt(0)->getId());
        $this->assertEquals(3, $trees->getAt(1)->getId());

    }


    public function testLimitAndOffsetWithJoin(){

        $q=Tree::faceQueryBuilder();
        $q->join("leafs");
        $q->setLimit(2);
        $q->setOffset(1);

        $sqlString = $q->getSqlString();

        $this->assertEquals("SELECT `this`.`id` AS `this.id`,`this`.`age` AS `this.age`,`this.leafs`.`id` AS `this.leafs.id`,`this.leafs`.`tree_id` AS `this.leafs.tree_id`,`this.leafs`.`length` AS `this.leafs.length` FROM (SELECT * FROM `tree` LIMIT 2 OFFSET 1) AS `this` LEFT JOIN `leaf` AS `this.leafs` ON `this`.`id`=`this.leafs`.`tree_id`", $sqlString);


        $pdo=$this->getConnection()->getConnection();
        $trees=\Face\ORM::execute($q,$pdo);
        $this->assertEquals(2, $trees->count());
        $this->assertEquals(2, $trees->getAt(0)->getId());
        $this->assertEquals(3, $trees->getAt(1)->getId());

    }

    /**
     * test the WhereINRelation with a belongsTo element as base
     *
     * @throws Exception
     */
    public function testWhereINRelationBelongsTo(){

        $pdo = $this->getConnection()->getConnection();

        // expected data
        $fQuery = Tree::faceQueryBuilder();
        $fQuery->join("leafs")->whereIN("~id",[1,2]);
        $res = Face\ORM::execute($fQuery, $pdo);
        $expected = $res->getInstancesByPath("this.leafs");
        $trees = $res->getInstancesByPath("this");

        $a = 0;

        $fQuery = new \Test\MockSelectBuilder(Leaf::getEntityFace());
        $fQuery->whereINRelation([
                function ($values) use(&$a){
                    $a++; // to test if it was executed
                    $this->assertEquals([1,2],$values); // we test that the values passed to IN are the one we want
                }
            ],
            "tree",$trees
        );


        $this->assertEquals(1,$a);

        $resActual = Face\ORM::execute($fQuery, $pdo);

        $this->assertEquals(count($expected), $resActual->count());

        foreach($expected as $e){
            $this->assertTrue($resActual->pathHasIdentity("this",$e->faceGetIdentity()));
        }

    }


    /**
     * test the WhereINRelation with a hasMany/hasOne element as base
     *
     * @throws Exception
     */
    public function testWhereINRelationHas___(){

        $pdo = $this->getConnection()->getConnection();

        // expected data
        $fQuery = Tree::faceQueryBuilder();
        $fQuery->join("leafs")->whereIN("~id",[1,2]);
        $res = Face\ORM::execute($fQuery, $pdo);
        $expected = $res->getInstancesByPath("this");
        $leafs = $res->getInstancesByPath("this.leafs");

        $a = 0;

        $fQuery = new \Test\MockSelectBuilder(Tree::getEntityFace());
        $fQuery->whereINRelation([
                function ($values) use(&$a){
                    $a++; // to test if it was executed
                    $this->assertEquals([1,2],$values); // we test that the values passed to IN are the one we want
                }
            ],
            "leafs",$leafs
        );



        $resActual = Face\ORM::execute($fQuery, $pdo);

        $this->assertEquals(count($expected), $resActual->count());
        $this->assertEquals(1,$a);

        foreach($expected as $e){
            $this->assertTrue($resActual->pathHasIdentity("this",$e->faceGetIdentity()));
        }

    }

    public function testWhereINRelationHasManyThoughNotJoined(){

        $pdo = $this->getConnection()->getConnection();

        // expected data
        $fQuery = Tree::faceQueryBuilder();
        $fQuery->join("childrenTrees")->whereIN("~id",[1,2]);


        $res = Face\ORM::execute($fQuery, $pdo);
        $expected = $res->getInstancesByPath("this");
        $childrenTrees = $res->getInstancesByPath("this.childrenTrees");

        $a = 0;
        $fQuery = new \Test\MockSelectBuilder(Tree::getEntityFace());
        $fQuery->whereINRelation([
            function ($values) use(&$a){
                $a++; // to test if it was executed
                $this->assertEquals([2,3,4],$values); // we test that the values passed to IN are the one we want
            }
        ],
            "childrenTrees",$childrenTrees
        );

        $this->assertEquals(1,$a);

        $resActual = Face\ORM::execute($fQuery, $pdo);


        $this->assertEquals(2, $resActual->count());

        $this->assertEquals(1,$resActual[0]->getId());
        $this->assertEquals(3,$resActual[1]->getId());

    }

    public function testWhereINRelationHasManyThoughAlreadyJoined(){

        $pdo = $this->getConnection()->getConnection();

        // expected data
        $fQuery = Tree::faceQueryBuilder();
        $fQuery->join("childrenTrees")->whereIN("~id",[1,2]);
        $res = Face\ORM::execute($fQuery, $pdo);
        $expected = $res->getInstancesByPath("this");
        $childrenTrees = $res->getInstancesByPath("this.childrenTrees");

        $a = 0;
        $fQuery = new \Test\MockSelectBuilder(Tree::getEntityFace());
        $fQuery->join("childrenTrees");
        $fQuery->whereINRelation([
            function ($values) use(&$a){
                $a++; // to test if it was executed
                $this->assertEquals([2,3,4],$values); // we test that the values passed to IN are the one we want
            }
        ],
            "childrenTrees",$childrenTrees
        );

        $this->assertEquals(1,$a);

        $resActual = Face\ORM::execute($fQuery, $pdo);

        $this->assertEquals(2, $resActual->count());

        $this->assertEquals(1,$resActual[0]->getId());
        $this->assertEquals(3,$resActual[1]->getId());

    }

    
    /**
     * @group hasManyThrough
     */
    public function testHasManyThrough()
    {

        $pdo = $this->getConnection()->getConnection();

        $fQuery= Tree::faceQueryBuilder();

        $fQuery->join("childrenTrees");

        $trees=  Face\ORM::execute($fQuery, $pdo);
        $children = $trees->getInstancesByPath("this.childrenTrees");


        $this->assertEquals(4,count($trees));

        $this->assertEquals(1,$trees[0]->getId());
        $this->assertEquals(8,$trees[0]->getAge());

        $this->assertEquals(2,$trees[1]->getId());
        $this->assertEquals(2,$trees[1]->getAge());

        $this->assertEquals(3,$trees[2]->getId());
        $this->assertEquals(5,$trees[2]->getAge());

        $this->assertEquals(4,$trees[3]->getId());
        $this->assertEquals(300,$trees[3]->getAge());



        $this->assertEquals(3, count($trees[0]->childrenTrees));
        $this->assertEquals(0, count($trees[1]->childrenTrees));
        $this->assertEquals(1, count($trees[2]->childrenTrees));
        $this->assertEquals(0, count($trees[3]->childrenTrees));

        $this->assertEquals(0, count($trees[0]->parentTrees));
        $this->assertEquals(1, count($trees[1]->parentTrees));
        $this->assertEquals(1, count($trees[2]->parentTrees));
        $this->assertEquals(2, count($trees[3]->parentTrees));

        $this->assertEquals($trees[0], $trees[2]->parentTrees[0]);


        $this->assertEquals(2,$trees[1]->getId());




    }

    public function testSimpleInsert(){

        $a = new Tree();
        $a->setId(200);
        $a->setAge(300);

        $insert = new Face\Sql\Query\SimpleInsert($a);

        $this->assertEquals("INSERT INTO `tree`(`age`) VALUES(:age)" , $insert->getSqlString() );
        $this->assertEquals(300 , $insert->getBoundValue(":age")[0] );

    }

    public function testSimpleUpdate(){

        $a = new Tree();
        $a->setId(200);
        $a->setAge(300);

        $update = new Face\Sql\Query\SimpleUpdate($a);

        $this->assertEquals("UPDATE `tree` SET `age`=:age WHERE `id`=:id LIMIT 1" , $update->getSqlString() );
        $this->assertEquals(300 , $update->getBoundValue(":age")[0] );
        $this->assertEquals(200 , $update->getBoundValue(":id")[0] );

    }




}
