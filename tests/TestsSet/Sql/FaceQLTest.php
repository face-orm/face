<?php


class FaceQLTest extends Test\PHPUnitTestDb
{

    public function testTokenize(){

        $parser = new \Face\Sql\Query\FaceQL();
        $tokens = $parser->tokenize("SELECT FROM tree JOIN lemons");
        $this->assertCount(4, $tokens);

    }

    public function testParseSelect(){
        $parser = new \Face\Sql\Query\FaceQL();
        /* @var $query \Face\Sql\Query\FQuery */
        $query = $parser->parse("SELECT FROM tree JOIN lemons");

        $this->assertInstanceOf("Face\Sql\Query\SelectBuilder", $query);
        $this->assertEquals("tree", $query->getBaseFace()->getName());

    }

}
