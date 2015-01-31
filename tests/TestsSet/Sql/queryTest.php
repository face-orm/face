<?php

class queryTest extends Test\PHPUnitTestDb
{
    /**
     * @group perf
     */
//    public function testPerf(){
//
//        $time = microtime(true);
//
//        $pdo = $this->getConnection()->getConnection();
//
//        $q=Tree::faceQueryBuilder()->join("lemons")->join("leafs")->join("lemons.seeds");
//
//        $trees=\Face\ORM::executeDebug($q,$pdo,$report);
//
//        $time = microtime(true) - $time;
//
//
//        echo "Total : " . $time *1000 . "ms" . PHP_EOL;
//        echo "Dont : ";var_export($report);
//
//        echo PHP_EOL . " Debugging value : " . \Face\Sql\Reader\QueryArrayReader::$devtimer  * 1000;
//
//
//    }

    
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

//        foreach ($trees as $tree){
//            echo "tree #".$tree->faceGetidentity()." - age : ".$tree->getAge().PHP_EOL;
//            foreach ($tree->getLemons() as $lemon){
//                echo " | lemon #". $lemon->faceGetidentity().PHP_EOL;
//                foreach ($lemon->getSeeds() as $seed){
//                    echo "   - seed ".$seed->faceGetidentity().PHP_EOL;
//                }
//            }
//            foreach ($tree->getLeafs() as $leaf){
//                echo " | leaf  ". $leaf->faceGetidentity().PHP_EOL;
//
//            }
//        }
        
        
//        var_dump($j);
        
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
    

 

}

