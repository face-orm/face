<?php



class FaceQLTest extends Test\PHPUnitTestDb
{



    protected function getDataSet()
    {
        return $this->createMySQLXMLDataSet('dataset.xml');
    }

    /**
     * @group faceql
     */
    public function testFrom(){

        $pdo=$this->getConnection()->getConnection();

        $fq=\Face\Sql\Query\FaceQL::parse(

            "SELECT::* FROM::Tree"

        );

        $trees = Face\ORM::execute($fq, $pdo);

        $this->assertEquals(4,count($trees));
        $this->assertEquals(1,$trees[0]->getId());
        $this->assertEquals(8,$trees[0]->getAge());


    }

    /**
     * @group faceql
     */
    public function testJoin(){


        $pdo=$this->getConnection()->getConnection();

        $fq=\Face\Sql\Query\FaceQL::parse(

            "SELECT::* FROM::Tree".
            " JOIN::lemons"

        );

        $trees = Face\ORM::execute($fq, $pdo);

        $this->assertEquals(4,count($trees));
        $this->assertEquals(12,count($trees->getInstancesByPath("this.lemons")));
        $this->assertEquals(1,$trees[0]->getId());
        $this->assertEquals(8,$trees[0]->getAge());


        $fq=\Face\Sql\Query\FaceQL::parse(

            "SELECT::* FROM::Tree".
            " JOIN::lemons".
            " JOIN::lemons.seeds"

        );

        $trees = Face\ORM::execute($fq, $pdo);

        $this->assertEquals(4,count($trees));
        $this->assertEquals(12,count($trees->getInstancesByPath("this.lemons")));
        $this->assertEquals(1,$trees[0]->getId());
        $this->assertEquals(8,$trees[0]->getAge());


    }

    /**
     * @group faceql
     */
    public function testWhere(){


        // TEST 1
        $pdo=$this->getConnection()->getConnection();

        $fq=\Face\Sql\Query\FaceQL::parse(

            "SELECT::* FROM::Tree".
            " JOIN::lemons".
            " WHERE ~id=1"

        );

        $trees = Face\ORM::execute($fq, $pdo);

        $this->assertEquals(1,count($trees));
        $this->assertEquals(1,$trees[0]->getId());
        $this->assertEquals(8,$trees[0]->getAge());


        // TEST 2
        $fq=\Face\Sql\Query\FaceQL::parse(

            "SELECT::* FROM::Tree".
            " JOIN::lemons".
            " WHERE ~id=:id"

        )->bindValue("id",1,PDO::PARAM_INT);


        $trees = Face\ORM::execute($fq, $pdo);

        $this->assertEquals(1,count($trees));
        $this->assertEquals(1,$trees[0]->getId());
        $this->assertEquals(8,$trees[0]->getAge());

    }


    /**
     * @group faceql
     */
    public function testBindIn(){


        // TEST 1
        $pdo=$this->getConnection()->getConnection();

        $fq=\Face\Sql\Query\FaceQL::parse(

            "SELECT::* FROM::Tree".
            " JOIN::lemons".
            " WHERE ~id IN (~:in:~)"

        )->bindIn("~:in:~",array(1,2,3));

        $trees = Face\ORM::execute($fq, $pdo);

        $this->assertEquals(3,count($trees));
        $this->assertEquals(array(1,8),array( $trees[0]->getId() , $trees[0]->getAge() ));
        $this->assertEquals(array(2,2),array( $trees[1]->getId() , $trees[1]->getAge() ));
        $this->assertEquals(array(3,5),array( $trees[2]->getId() , $trees[2]->getAge() ));

    }



}
