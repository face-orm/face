<?php



class ResultsetTest extends PHPUnit_Framework_TestCase
{


    public function testConstruction(){

        $face = Tree::getEntityFace();

        $r = new \Face\Sql\Result\ResultSet($face);

    }

    public function testBasics(){

        $face = Tree::getEntityFace();

        $r = new \Face\Sql\Result\ResultSet($face);

        // testgetBaseInstanceEmpty
        $this->assertEquals([],$r->getBaseInstances());


        $tree1 = new Tree();
        $tree1->setId(1);
        $tree1->setAge(1);

        $tree2 = new Tree();
        $tree2->setId(2);
        $tree2->setAge(2);

        $lemon1 = new Lemon();
        $lemon1->setId(1);
        $lemon1->setMature(1);

        $lemon2 = new Lemon();
        $lemon2->setId(2);
        $lemon2->setMature(2);
    }


}
