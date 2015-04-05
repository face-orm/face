<?php

class EntityFaceElementTest extends PHPUnit_Framework_TestCase {

    public function testBuildFace(){

        $expected = Tree::getEntityFace();

        $actual = \Face\Core\FaceFactory::buildFace($expected);

        $this->assertSame($expected,$actual);

    }

    public function testGetterAndSetters(){

        $e = new \Face\Core\EntityFaceElement("test");
        $this->assertEquals("test", $e->getName());

        $e->setPropertyName("testProperty");
        $this->assertEquals("testProperty", $e->getPropertyName());

        $this->assertEquals(false, $e->hasGetter());
        $e->setGetter("testGetter");
        $this->assertEquals("testGetter", $e->getGetter());
        $this->assertEquals(true, $e->hasGetter());

        $this->assertEquals(false, $e->hasSetter());
        $e->setSetter("testSetter");
        $this->assertEquals("testSetter", $e->getSetter());
        $this->assertEquals(true, $e->hasSetter());

        $e->setSqlThrough("testSqlThrough");
        $this->assertEquals("testSqlThrough", $e->getSqlThrough());

        $this->assertEquals("value", $e->getType());
        $this->assertEquals(true, $e->isValue());
        $this->assertEquals(false, $e->isEntity());
        $e->setType("entity");
        $this->assertEquals("entity", $e->getType());
        $this->assertEquals(false, $e->isValue());
        $this->assertEquals(true, $e->isEntity());

        $this->assertEquals(false, $e->isIdentifier());
        $e->setIsIdentifier(true);
        $this->assertEquals(true, $e->isIdentifier());

        $e->setSqlColumnName("sqlColTest");
        $this->assertEquals("sqlColTest", $e->getSqlColumnName());

        $this->assertEquals(false, $e->isPrimary());
        $e->setSqlIsPrimary(true);
        $this->assertEquals(true, $e->isPrimary());

        $e->setSqlJoin("testJoin");
        $this->assertEquals("testJoin", $e->getSqlJoin());

        $e->setSqlAutoIncrement(true);
        $this->assertEquals(true, $e->getSqlAutoIncrement());


        $e->setRelation("hasMany");
        $this->assertTrue($e->hasManyRelationship());
        $this->assertTrue($e->relationIsHas___());

        $e->setRelation("hasManyThrough");
        $this->assertTrue($e->hasManyThroughRelationship());
        $this->assertTrue($e->relationIsHas___());

        $e->setRelation("belongsTo");
        $this->assertTrue($e->relationIsBelongsTo());
        $this->assertFalse($e->relationIsHas___());

        $e->setRelation("hasOne");
        $this->assertTrue($e->relationIsHas___());


        $e->setType("value");
        try{
            $e->getFace();
            $this->fail("Should throw exception");
        }catch(\Exception $e){
            $this->assertInstanceOf("Face\Exception",$e);
        }


    }
}
