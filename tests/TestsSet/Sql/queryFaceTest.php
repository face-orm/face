<?php

use Face\Sql\Query\SelectBuilder\QueryFace;
use Face\Sql\Query\Clause\Select\Column;

class queryFaceTest extends Test\PHPUnitTestDb
{


    public function testConstruction(){

        $queryFace = new QueryFace("this", Tree::getEntityFace());

        $this->assertEquals("this", $queryFace->getPath());
        $this->assertEquals(Tree::getEntityFace(), $queryFace->getFace());
        $this->assertEquals([], $queryFace->getColumns());

        $queryFace->setColumns(["*"]);
        $this->assertEquals(["*"], $queryFace->getColumns());

        $columnsReal = $queryFace->getColumnsReal();

        $this->assertEquals(2, count($columnsReal));
        $this->assertEquals("this.id", $columnsReal["this.id"]->getQueryAlias());
        $this->assertEquals("this.age", $columnsReal["this.age"]->getQueryAlias());
        $this->assertEquals("this.id", $columnsReal["this.id"]->getPath());
        $this->assertEquals("this.age", $columnsReal["this.age"]->getPath());
        $this->assertEquals(Tree::getEntityFace()->getDirectElement("id"),  $columnsReal["this.id"]->getEntityFaceElement());
        $this->assertEquals(Tree::getEntityFace()->getDirectElement("age"), $columnsReal["this.age"]->getEntityFaceElement());


        $queryFace->addColumn("!id");
        // remove id, but id columns cant be removed; we run the same tests
        $columnsReal = $queryFace->getColumnsReal();
        $this->assertEquals(2, count($columnsReal));
        $this->assertEquals("this.id", $columnsReal["this.id"]->getQueryAlias());
        $this->assertEquals("this.age", $columnsReal["this.age"]->getQueryAlias());
        $this->assertEquals("this.id", $columnsReal["this.id"]->getPath());
        $this->assertEquals("this.age", $columnsReal["this.age"]->getPath());
        $this->assertEquals(Tree::getEntityFace()->getDirectElement("id"),  $columnsReal["this.id"]->getEntityFaceElement());
        $this->assertEquals(Tree::getEntityFace()->getDirectElement("age"), $columnsReal["this.age"]->getEntityFaceElement());


        $queryFace->addColumn("!age");
        // age is not an identifier, it will be deleted
        $columnsReal = $queryFace->getColumnsReal();
        $this->assertEquals(1, count($columnsReal));
        $this->assertEquals("this.id", $columnsReal["this.id"]->getQueryAlias());
        $this->assertEquals("this.id", $columnsReal["this.id"]->getPath());
        $this->assertEquals(Tree::getEntityFace()->getDirectElement("id"),  $columnsReal["this.id"]->getEntityFaceElement());


        $queryFace->addColumn("age","ega");
        $columnsReal = $queryFace->getColumnsReal();
        $this->assertEquals(2, count($columnsReal));
        $this->assertEquals("this.id", $columnsReal["this.id"]->getQueryAlias());
        $this->assertEquals("ega", $columnsReal["this.age"]->getQueryAlias());
        $this->assertEquals("this.id", $columnsReal["this.id"]->getPath());
        $this->assertEquals("this.age", $columnsReal["this.age"]->getPath());
        $this->assertEquals(Tree::getEntityFace()->getDirectElement("id"),  $columnsReal["this.id"]->getEntityFaceElement());
        $this->assertEquals(Tree::getEntityFace()->getDirectElement("age"), $columnsReal["this.age"]->getEntityFaceElement());


        $queryFace->setColumns(["id","age"=>"ega"]);
        // Exactly the same tests as previously
        $columnsReal = $queryFace->getColumnsReal();
        $this->assertEquals(2, count($columnsReal));
        $this->assertEquals("this.id", $columnsReal["this.id"]->getQueryAlias());
        $this->assertEquals("ega", $columnsReal["this.age"]->getQueryAlias());
        $this->assertEquals("this.id", $columnsReal["this.id"]->getPath());
        $this->assertEquals("this.age", $columnsReal["this.age"]->getPath());
        $this->assertEquals(Tree::getEntityFace()->getDirectElement("id"),  $columnsReal["this.id"]->getEntityFaceElement());
        $this->assertEquals(Tree::getEntityFace()->getDirectElement("age"), $columnsReal["this.age"]->getEntityFaceElement());


        $queryFace->setColumns(null);
        $this->assertEquals([], $queryFace->getColumns());

        try{
            $queryFace->setColumns("test");
            $this->fail("Exception expected");
        }catch (\Face\Exception\BadParameterException $e){
            $this->assertInstanceOf("\Face\Exception\BadParameterException", $e);
        }

    }

    public function testSelectColmunClauseString(){

        $query = new \Face\Sql\Query\SelectBuilder(Tree::getEntityFace());

        $column = new Column\ElementColumn("this", Tree::getEntityFace()->getDirectElement("id"));
        $column->setQueryAlias("aliasId");

        $expected = "`this`.`id` AS `aliasId`";

        $this->assertEquals($expected, $column->getSqlString($query));

    }



}

