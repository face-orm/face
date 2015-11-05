<?php


class ColumnSelectTest extends Test\PHPUnitTestDb
{
    /**
     * @var \Face\Sql\Query\SelectQuery
     */
    protected  $query;

    public  function setUp()
    {
        $treeFace = \Face\Config::getDefault()->getFaceLoader()->getFaceForName("tree");
        $this->query = new \Face\Sql\Query\SelectQuery($treeFace);
    }


    public function testSelectIdColumnOnly(){
        $this->query->getBaseQueryFace()->addColumn("id");
        $trees = \Face\ORM::execute($this->query, $this->getConnection()->getConnection());

        foreach($trees as $tree){
            $this->assertNull($tree->getAge());
        }
    }

    public function testOneDataColumnOnly(){
        $this->query->getBaseQueryFace()->addColumn("age");
        $trees = \Face\ORM::execute($this->query, $this->getConnection()->getConnection());

        foreach($trees as $tree){
            $this->assertGreaterThan(0, $tree->getAge());
        }
    }

    public function testCountLemons(){
        $lemonFace = \Face\Config::getDefault()->getFaceLoader()->getFaceForName("lemon");
        $join = new \Face\Sql\Query\SelectBuilder\JoinQueryFace("lemons", $lemonFace);
        $join->setSilent(true);
        $this->query->addJoin($join);



        $countColumn = new \Face\Sql\Query\Clause\Select\Column\ExpressionColumn("COUNT(~lemons.id)", "this.lemon_count");
        $this->query->addColumn($countColumn);

        $groupByColumn = new \Face\Sql\Query\Clause\Select\Column\ElementColumn("this", $this->query->getBaseFace()->getDirectElement("id"));
        $groupBy = new \Face\Sql\Query\Clause\GroupBy([$groupByColumn]);
        $this->query->setGroupBy($groupBy);

        $trees = \Face\ORM::execute($this->query, $this->getConnection()->getConnection());

        $this->assertCount(4, $trees);

        $expect = [
            [1,   8, 6],
            [2,   2, 1],
            [3,   5, 5],
            [4, 300, 0]
        ];

        foreach($expect as $index=>$data){
            $this->assertEquals($data[0], $trees->getAt($index)->getId());
            $this->assertEquals($data[1], $trees->getAt($index)->getAge());
            $this->assertEquals($data[2], $trees->getAt($index)->lemon_count);
        }


    }
}
