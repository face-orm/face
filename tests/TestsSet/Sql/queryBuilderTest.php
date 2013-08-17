<?php


class queryBuilderTest extends Test\PHPUnitTestDb
{



    protected function getDataSet()
    {
        return $this->createMySQLXMLDataSet('dataset.xml');
    }


    public function testSelect()
    {

        
    }

    public function testSimpleInsert(){

        $a = new Tree();
        $a->setId(200);
        $a->setAge(300);

        $insert = new Face\Sql\Query\SimpleInsert($a);


        $this->assertEquals("INSERT INTO tree(age) VALUES(:age)" , $insert->getSqlString() );
        $this->assertEquals(300 , $insert->getBoundValue(":age")[0] );

    }

    public function testSimpleUpdate(){

        $a = new Tree();
        $a->setId(200);
        $a->setAge(300);

        $update = new Face\Sql\Query\SimpleUpdate($a);

        $this->assertEquals("UPDATE tree SET age=:age WHERE id=:id LIMIT 1" , $update->getSqlString() );
        $this->assertEquals(300 , $update->getBoundValue(":age")[0] );
        $this->assertEquals(200 , $update->getBoundValue(":id")[0] );

    }




}
