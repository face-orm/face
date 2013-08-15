<?php



class FaceQLTest extends Test\PHPUnitTestDb
{



    protected function getDataSet()
    {
        return $this->createMySQLXMLDataSet('dataset.xml');
    }


    public function testParseBaseFace(){

        $fql=new \Face\Sql\Query\FaceQL("SELECT::* FROM::Tree");

        $this->assertEquals(Tree::getEntityFace(),$fql->getBaseFace());

    }

    public function testParseJoinFace(){

        $q=
            "SELECT::* FROM::Tree".
            " JOIN::lemon";

        $fql=new \Face\Sql\Query\FaceQL($q);

        $this->assertEquals(Tree::getEntityFace(),$fql->getBaseFace());

    }




}
