<?php

class queryTest extends Test\PHPUnitTestDb
{
    public function testGetter()
    {

        $pdo = $this->getConnection()->getConnection();
        

        
        $fQuery= Tree::faceQueryBuilder();

        $fQuery->join("lemons")->join("lemons.seeds")->join("leafs");

        //->bindValue(":name", "%A%");


        $trees=  Face\ORM::execute($fQuery, $pdo);


        $this->assertEquals(4,count($trees));
        $this->assertEquals(12,count($trees->getInstancesByPath("this.lemons")));
        $this->assertEquals(1,$trees[0]->getId());
        $this->assertEquals(8,$trees[0]->getAge());
        
    }

    public function testInsert(){


        $pdo = $this->getConnection()->getConnection();

        $a = new Tree();
        $a->setAge(301);

        $insert = new Face\Sql\Query\SimpleInsert($a);
        $insert->execute($pdo);

        $fQuery= Tree::faceQueryBuilder();
        $fQuery->where('~age=:age')
            ->bindValue(':age',301,PDO::PARAM_INT);

        $tree = Face\ORM::execute($fQuery, $pdo)[0];
        $this->assertEquals(5,$tree->getId());

    }



    public function testSimpleDelete(){


        $pdo = $this->getConnection()->getConnection();

        $a = new Seed();
        $a->setId(1);

        $delete = new Face\Sql\Query\SimpleDelete($a);

        $this->assertEquals("DELETE FROM `seed` WHERE `id`=:id LIMIT 1", $delete->getSqlString());

        $delete->execute($pdo);

        $fQuery= Seed::faceQueryBuilder();
        $seeds = Face\ORM::execute($fQuery, $pdo);
        $this->assertEquals(7,$seeds->count());

        $fQuery= Seed::faceQueryBuilder();
        $fQuery->where('~id=:id')
            ->bindValue(':id',1,PDO::PARAM_INT);
        $seeds = Face\ORM::execute($fQuery, $pdo);
        $this->assertEquals(0,$seeds->count());

    }
    

 

}

