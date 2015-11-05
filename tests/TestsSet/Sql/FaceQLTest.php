<?php


class FaceQLTest extends Test\PHPUnitTestDb
{
    protected  $_defaultConfig;

    public  function setUp()
    {
        parent::setUp();

        $this->_defaultConfig = \Face\Config::getDefault();

        $newConfig = new \Face\Config();
        $newConfig->setFaceLoader($this->_defaultConfig->getFaceLoader());
        $newConfig->setPdo($this->getConnection()->getConnection());

        \Face\Config::setDefault($newConfig);

    }

    public  function tearDown()
    {
        parent::tearDown();
        \Face\Config::setDefault($this->_defaultConfig);
    }


    public function testTokenize(){

        $parser = new \Face\Sql\Query\FaceQL();
        $tokens = $parser->tokenize("SELECT FROM tree JOIN lemons");
        $this->assertCount(4, $tokens);

    }

    public function testParseSelect(){
        $parser = new \Face\Sql\Query\FaceQL();
        /* @var $query \Face\Sql\Query\FQuery */
        $query = $parser->parse("SELECT FROM tree");

        $this->assertInstanceOf("Face\Sql\Query\SelectBuilder", $query);
        $this->assertEquals("tree", $query->getBaseFace()->getName());

        $trees = \Face\ORM::execute($query);

        $this->assertCount(4, $trees);
        $this->assertInstanceOf("Tree", $trees->getAt(0));
        $this->assertEquals(8, $trees->getAt(0)->getAge());

        $this->setExpectedException("Face\Exception\FQLParseException");
        $parser->parse("SELECT FROM tree select");
    }

    public function testParseJoinLemons(){
        $parser = new \Face\Sql\Query\FaceQL();
        /* @var $query \Face\Sql\Query\FQuery */
        $query = $parser->parse("SELECT FROM tree JOIN lemons");

        $this->assertInstanceOf("Face\Sql\Query\SelectBuilder", $query);
        $this->assertEquals("tree", $query->getBaseFace()->getName());
        $this->assertCount(1, $query->getJoins());
        $this->assertEquals("lemon", current($query->getJoins())->getFace()->getName());

        $trees = \Face\ORM::execute($query);

        $this->assertCount(4, $trees);
        $this->assertInstanceOf("Tree", $trees->getAt(0));
        $this->assertEquals(8, $trees->getAt(0)->getAge());
        $this->assertCount(6, $trees->getAt(0)->getLemons());
    }

    public function testParseJoinLemonsJoinSeeds(){
        $parser = new \Face\Sql\Query\FaceQL();
        /* @var $query \Face\Sql\Query\FQuery */
        $query = $parser->parse("SELECT FROM tree JOIN lemons JOIN lemons.seeds");

        $this->assertInstanceOf("Face\Sql\Query\SelectBuilder", $query);
        $this->assertEquals("tree", $query->getBaseFace()->getName());
        $this->assertCount(2, $query->getJoins());
        $this->assertEquals("lemon", current($query->getJoins())->getFace()->getName());
        $this->assertEquals("seed", next($query->getJoins())->getFace()->getName());

        $trees = \Face\ORM::execute($query);

        $this->assertCount(4, $trees);
        $this->assertInstanceOf("Tree", $trees->getAt(0));
        $this->assertEquals(8, $trees->getAt(0)->getAge());
        $this->assertCount(6, $trees->getAt(0)->getLemons());
        $this->assertCount(3, $trees->getAt(0)->getLemons()[0]->getSeeds());
    }


    public function testParseJoinLemonsLimit(){
        $parser = new \Face\Sql\Query\FaceQL();
        /* @var $query \Face\Sql\Query\FQuery */
        $query = $parser->parse("SELECT FROM tree LIMIT 1");

        $this->assertInstanceOf("Face\Sql\Query\SelectBuilder", $query);
        $this->assertEquals(1, $query->getLimit());

        $trees = \Face\ORM::execute($query);

        $this->assertCount(1, $trees);
        $this->assertInstanceOf("Tree", $trees->getAt(0));
        $this->assertEquals(8, $trees->getAt(0)->getAge());
    }

}
