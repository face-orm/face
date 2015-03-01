<?php

class DiOrmTest extends Test\PHPUnitTestDb
{


    protected  $_defaultConfig;

    public  function setUp()
    {
        parent::setUp();

        $this->_defaultConfig = \Face\Config::getDefault();
        \Face\Config::setDefault(null);

    }

    public  function tearDown()
    {
        parent::tearDown();
        \Face\Config::setDefault($this->_defaultConfig);
    }


    protected function getDiOrm(){

        $config = new \Face\Config();
        $config->setPdo($this->getConnection()->getConnection());
        $config->setFaceLoader( new \Face\Core\FaceLoader\FileReader\PhpArrayReader( __DIR__ . "/../../res/model-definitions/arrayList" ) );


        $diOrm = new \Face\DiORM($config);

        return $diOrm;

    }

    
    public function testDiOrmSelect()
    {

        $fQuery= Tree::faceQueryBuilder($this->getDiOrm()->getConfig());

        $fQuery
            ->join("lemons")
            ->join("lemons.seeds")
            ->join("leafs");


        $trees=  $this->getDiOrm()->select($fQuery);


        $this->assertEquals(4,count($trees));
        $this->assertEquals(12,count($trees->getInstancesByPath("this.lemons")));
        $this->assertEquals(1,$trees[0]->getId());
        $this->assertEquals(8,$trees[0]->getAge());

    }

    public function testSimpleInsert(){


        $a = new Tree();
        $a->setAge(301);

        $insertResult = $this->getDiOrm()->simpleInsert($a);

        $this->assertEquals(5 , $insertResult->getInsertId() );
        $this->assertEquals(1 , $insertResult->countAffectedRows() );
        $this->isInstanceOf("PDOStatement" , $insertResult->getPdoStatement() );

        $fQuery= Tree::faceQueryBuilder($this->getDiOrm()->getConfig());
        $fQuery->where('~age=:age')
            ->bindValue(':age',301,PDO::PARAM_INT);

        $tree = $this->getDiOrm()->select($fQuery)[0];
        $this->assertEquals(5,$tree->getId());

    }

    public function testSimpleUpdate(){

        $tree = new Tree();
        $tree->setId(1);
        $tree->setAge(2101);

        $updateResult = $this->getDiOrm()->simpleUpdate($tree);

        $this->assertEquals(1 , $updateResult->countAffectedRows() );
        $this->isInstanceOf("PDOStatement" , $updateResult->getPdoStatement() );

        $fQuery= Tree::faceQueryBuilder($this->getDiOrm()->getConfig());
        $fQuery->where('~id=:id')
            ->bindValue(':id',1,PDO::PARAM_INT);
        $tree = $this->getDiOrm()->select($fQuery)[0];

        $this->assertEquals(2101,$tree->getAge());

    }



    public function testSimpleDelete(){

        $seed = new Seed();
        $seed->setId(1);

        $deleteResult = $this->getDiOrm()->simpleDelete($seed);

        $this->assertEquals(1 , $deleteResult->countAffectedRows() );
        $this->isInstanceOf("PDOStatement" , $deleteResult->getPdoStatement() );

        $fQuery= Seed::faceQueryBuilder($this->getDiOrm()->getConfig());
        $fQuery->where('~id=:id')
            ->bindValue(':id',1,PDO::PARAM_INT);
        $seeds = $this->getDiOrm()->select($fQuery);

        $this->assertEquals(0,$seeds->count());

    }
    

    public function testSelectBuilder(){

        $select = $this->getDiOrm()->selectBuilder("tree");

        $select->join("lemons")
            ->join("lemons.seeds")
            ->join("leafs");

        $trees = $select->results();

        $this->assertEquals(4,count($trees));
        $this->assertEquals(12,count($trees->getInstancesByPath("this.lemons")));
        $this->assertEquals(1,$trees[0]->getId());
        $this->assertEquals(8,$trees[0]->getAge());

    }
 

}

