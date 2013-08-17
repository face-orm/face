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
        
        $pieces;
        $this->assertEquals($AFace->getElement("b.c",1,$pieces)->getFace(), $BFace);
        $this->assertEquals("c", $pieces[1]);
        $this->assertEquals("b", $pieces[0]);
    }
    
    public function testDefaultMap()
    {
        $map=A::faceDefaultMap();
        $this->assertEquals(['a'=>'a_column'], $map);
        
        $map=A::faceDefaultMap(['a']);
        $this->assertEquals([], $map);
        
        $map=A::faceDefaultMap([],['a'=>'onea']);
        $this->assertEquals(['a'=>'onea'], $map);
        
        $map=A::faceDefaultMap([],["b"=>'oneb']);
        $this->assertEquals(['a'=>'a_column',"b"=>'oneb'], $map);
        
        $map=A::faceDefaultMap([],['a'=>'onea',"b"=>'oneb']);
        $this->assertEquals(['a'=>'onea',"b"=>'oneb'], $map);
    }
 

}
