<?php


class LineTest extends PHPUnit_Framework_TestCase
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
 

}


class A{
    use \Face\Traits\EntityFaceTrait;
    
    protected $a;
    protected $b;
    
    public static function __getEntityFace() {
        return [
            
            "elements"=>[
                "a"=>[
                    "propertyName"  =>  "a",
                    "type"          =>  "value",
                ],
                "b"=>[
                    "propertyName"  =>  "b",
                    "type"          =>  "entity",
                    "class"         =>  "B",
                    
                ]
            ]
            
        ];
    }
    public function getA() {
        return $this->a;
    }

    public function setA($a) {
        $this->a = $a;
    }

    public function getB() {
        return $this->b;
    }

    public function setB($b) {
        $this->b = $b;
    }


}

class B{
    use \Face\Traits\EntityFaceTrait;
    
    protected $name;

    
    public static function __getEntityFace() {
        return [
            
            "elements"=>[
                "name"=>[
                    "propertyName"=>"name",
                    "type"=>"value",
                ],
            ]
            
        ];
    }
    
    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }




    
}

?>
