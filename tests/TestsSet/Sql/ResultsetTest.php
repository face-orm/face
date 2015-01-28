<?php



class ResultsetTest extends PHPUnit_Framework_TestCase
{


    public function testConstruction(){

        $face = Tree::getEntityFace();
        $ik = new \Face\Core\InstancesKeeper();

        $r = new \Face\Sql\Result\ResultSet($face,$ik);

        $this->assertSame($ik,$r->getInstanceKeeper());


    }


    public function testGetter(){

        $face = Tree::getEntityFace();
        $ik = new \Face\Core\InstancesKeeper();

        $r = new \Face\Sql\Result\ResultSet($face,$ik);

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

        $r->addInstanceByPath("this",$tree1,$tree1->faceGetIdentity());
        $r->addInstanceByPath("this",$tree2,$tree2->faceGetIdentity());

        $r->addInstanceByPath("this.lemons",$lemon1,$lemon1->faceGetIdentity());
        $r->addInstanceByPath("this.lemons",$lemon2,$lemon2->faceGetIdentity());


        // test getInstancesByPath

        $noPath = $r->getInstancesByPath();

        $this->assertSame([
            "this"=>[$tree1,$tree2],
            "this.lemons"=>[$lemon1,$lemon2]
        ],$noPath);

        $withPath = $r->getInstancesByPath("this.lemons");

        $this->assertSame([$lemon1,$lemon2],$withPath);



        // testgetBaseInstance

        $this->assertSame($r->getInstancesByPath("this"),$r->getBaseInstances());


    }


}
