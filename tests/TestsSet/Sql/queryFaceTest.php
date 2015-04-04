<?php

use \Face\Sql\Query\SelectBuilder\QueryFace;

class queryFaceTest extends Test\PHPUnitTestDb
{


    public function testConstruction(){

        $queryFace = new QueryFace("this", Tree::getEntityFace());

        $queryFace->setLimit(5);
        $queryFace->setOffset(8);



        $this->assertEquals(5, $queryFace->getLimit());
        $this->assertEquals(8, $queryFace->getOffset());
        $this->assertEquals("this", $queryFace->getPath());
        $this->assertEquals(Tree::getEntityFace(), $queryFace->getFace());
        $this->assertEquals([], $queryFace->getColumns());

        $queryFace->setColumns(["*"]);
        $this->assertEquals(["*"], $queryFace->getColumns());

        $columnsReal = $queryFace->getColumnsReal();

        $this->assertEquals(2, count($columnsReal));
        $this->assertEquals("this.id", $columnsReal["this.id"]->getAlias());
        $this->assertEquals("this.age", $columnsReal["this.age"]->getAlias());
        $this->assertEquals("this.id", $columnsReal["this.id"]->getPath());
        $this->assertEquals("this.age", $columnsReal["this.age"]->getPath());
        $this->assertEquals(Tree::getEntityFace()->getDirectElement("id"),  $columnsReal["this.id"]->getEntityFaceElement());
        $this->assertEquals(Tree::getEntityFace()->getDirectElement("age"), $columnsReal["this.age"]->getEntityFaceElement());


        $queryFace->addColumn("!id");
        // remove id, but id columns cant be removed; we run the same tests
        $columnsReal = $queryFace->getColumnsReal();
        $this->assertEquals(2, count($columnsReal));
        $this->assertEquals("this.id", $columnsReal["this.id"]->getAlias());
        $this->assertEquals("this.age", $columnsReal["this.age"]->getAlias());
        $this->assertEquals("this.id", $columnsReal["this.id"]->getPath());
        $this->assertEquals("this.age", $columnsReal["this.age"]->getPath());
        $this->assertEquals(Tree::getEntityFace()->getDirectElement("id"),  $columnsReal["this.id"]->getEntityFaceElement());
        $this->assertEquals(Tree::getEntityFace()->getDirectElement("age"), $columnsReal["this.age"]->getEntityFaceElement());


        $queryFace->addColumn("!age");
        // age is not an identifier, it will be deleted
        $columnsReal = $queryFace->getColumnsReal();
        $this->assertEquals(1, count($columnsReal));
        $this->assertEquals("this.id", $columnsReal["this.id"]->getAlias());
        $this->assertEquals("this.id", $columnsReal["this.id"]->getPath());
        $this->assertEquals(Tree::getEntityFace()->getDirectElement("id"),  $columnsReal["this.id"]->getEntityFaceElement());


        $queryFace->addColumn("age","ega");
        $columnsReal = $queryFace->getColumnsReal();
        $this->assertEquals(2, count($columnsReal));
        $this->assertEquals("this.id", $columnsReal["this.id"]->getAlias());
        $this->assertEquals("ega", $columnsReal["this.age"]->getAlias());
        $this->assertEquals("this.id", $columnsReal["this.id"]->getPath());
        $this->assertEquals("this.age", $columnsReal["this.age"]->getPath());
        $this->assertEquals(Tree::getEntityFace()->getDirectElement("id"),  $columnsReal["this.id"]->getEntityFaceElement());
        $this->assertEquals(Tree::getEntityFace()->getDirectElement("age"), $columnsReal["this.age"]->getEntityFaceElement());


    }

 

}

