<?php



class FaceQLTest extends Test\PHPUnitTestDb
{



    protected function getDataSet()
    {
        return $this->createMySQLXMLDataSet('dataset.xml');
    }

    public function testA(){

        \Face\Sql\Query\FaceQL::parse(

            "SELECT::* FROM::Tree".
            " JOIN::lemons"

        );

        \Face\Sql\Query\FaceQL::parse(

            "SELECT::* FROM::Tree".
            " JOIN::lemons".
            " JOIN::lemons.seeds"

        );

    }


//
//
//    public function testParseBaseFace(){
//
//        $fql=new \Face\Sql\Query\FaceQL("SELECT::* FROM::Tree");
//
//        $this->assertEquals(Tree::getEntityFace(),$fql->getBaseFace());
//
//    }
//
//    public function testParseJoinFace(){
//
//        $q=
//            "SELECT::* FROM::Tree".
//            " JOIN::lemons";
//
//        $fql=new \Face\Sql\Query\FaceQL($q);
//
//        $this->assertEquals(Tree::getEntityFace(),$fql->getBaseFace());
//
//
//        $q=
//            "SELECT::* FROM::Tree".
//            " JOIN::lemons".
//            " JOIN::lemons.seeds";
//
//        $fql=new \Face\Sql\Query\FaceQL($q);
//
//    }




}
