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
        $this->assertEquals(13,count($trees->getInstancesByPath("this.lemons")));
        $this->assertEquals(1,$trees[0]->getId());
        $this->assertEquals(8,$trees[0]->getAge());


        $fq=\Face\Sql\Query\FaceQL::parse(

            "SELECT::* FROM::Tree".
            " JOIN::lemons".
            " JOIN::lemons.seeds"

        );

        $trees = Face\ORM::execute($fq, $pdo);

        $this->assertEquals(4,count($trees));
        $this->assertEquals(13,count($trees->getInstancesByPath("this.lemons")));
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



}
