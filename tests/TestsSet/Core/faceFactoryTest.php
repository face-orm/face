<?php



class FaceFactoryTest extends PHPUnit_Framework_TestCase
{

    public function testBuildFace(){

        $expected = Tree::getEntityFace();

        $actual = \Face\Core\FaceFactory::buildFace($expected);

        $this->assertSame($expected,$actual);

    }

}
