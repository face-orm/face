<?php


class GeneratedHyratorTest extends Test\PHPUnitTestDb
{

    public function testSomething(){
        $pdo = $this->getConnection()->getConnection();
        $fQuery= Tree::faceQueryBuilder();

        $fQuery->join("lemons")->join("lemons.seeds")->join("leafs");

        $statement = $fQuery->execute($pdo);

        $hydrator = new \Face\Sql\Hydrator\Generated\ArrayHydrator();

        $data = $hydrator->hydrate($fQuery, $statement);


        $data->getAt(0)
            ->getLemons()[0]
            ->getTree()
            ->getLemons()[0]
            ->getTree()
            ->getLemons()[0]
            ->getTree()
        ;

    }

}
