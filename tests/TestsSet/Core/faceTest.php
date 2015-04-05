<?php


class FaceTest extends PHPUnit_Framework_TestCase
{
    
    public function testGetter()
    {

        $b=new B();
        $b->setName("B string");
        
        $a=new A();
        
        $a->setA("A string");
        $a->setB($b);
        
        $this->assertEquals("A string", $a->faceGetter("a"));
        $this->assertEquals("B string", $b->faceGetter("name"));
        $this->assertEquals($b, $a->faceGetter("b"));
        $this->assertEquals("B string", $a->faceGetter("b.name"));
        
    }
    
    public function testSetter()
    {
        $b=new B();
        $b->faceSetter("name","my B");
        $this->assertEquals("my B", $b->getName());
        $this->assertEquals("my B", $b->faceGetter("name"));
        
        $a=new A();
        $a->faceSetter("b",$b);
        $this->assertEquals($b, $a->faceGetter("b"));
        $this->assertEquals("my B", $a->faceGetter("b.name"));
        
        $a->faceSetter("b.name", "new B str");
        $this->assertEquals("new B str", $a->faceGetter("this.b.name"));
        
    }
    
    public function testRecursiveGetFace()
    {
        $AFace=A::getEntityFace();
        $BFace=B::getEntityFace();
        $CFace=C::getEntityFace();
        
        
        $this->assertEquals($AFace->getElement("b.c")->getFace(), $CFace);
        
        $pieces=[];
        $this->assertEquals($AFace->getElement("b.c",1,$pieces)->getFace(), $BFace);
        $this->assertEquals("c", $pieces[1]);
        $this->assertEquals("b", $pieces[0]);
    }

    public function testException(){

        $a = new A();
        try{
            $a->faceGetter(5);
            $this->fail("Should throw exception");
        }catch(\Exception $e){
            $this->assertInstanceOf("Face\Exception\BadParameterException",$e);
        }

        try{
            $a->getEntityFace()->getElement("b",-1);
            $this->fail("Should throw exception");
        }catch(\Exception $e){
            $this->assertInstanceOf("Face\Exception\BadParameterException",$e);
        }

        try{
            $a->getEntityFace()->getElement("bb");
            $this->fail("Should throw exception");
        }catch(\Exception $e){
            // todo  check error message items
            $this->assertInstanceOf("Face\Exception\FaceElementDoesntExistsException",$e);
        }

        try{
            $a->getEntityFace()->getElement("ab");
            $this->fail("Should throw exception");
        }catch(\Exception $e){
            // todo  check error message items
            $this->assertInstanceOf("Face\Exception\FaceElementDoesntExistsException",$e);
        }


    }


    public function testConstruction(){

        $face = new \Face\Core\EntityFace();

        $face->setClass("testClass");
        $this->assertEquals("testClass", $face->getClass());

    }

 

}
