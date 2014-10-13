<?php


class queryBuilderTest extends Test\PHPUnitTestDb
{



    protected function getDataSet()
    {
        return $this->createMySQLXMLDataSet('dataset.xml');
    }


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
    
    /**
     * @group hasManyThrough
     */
    public function testHasManyThrough()
    {

        $pdo = $this->getConnection()->getConnection();
        

        
        $fQuery= Tree::faceQueryBuilder();

        $fQuery->join("childrenTrees");



        $trees=  Face\ORM::execute($fQuery, $pdo);


        $this->assertEquals(4,count($trees));
        $this->assertEquals(1,$trees[0]->getId());
        $this->assertEquals(8,$trees[0]->getAge());


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

        $this->assertEquals("UPDATE tree SET age=:age WHERE id=:id LIMIT 1" , $update->getSqlString() );
        $this->assertEquals(300 , $update->getBoundValue(":age")[0] );
        $this->assertEquals(200 , $update->getBoundValue(":id")[0] );

    }




}
