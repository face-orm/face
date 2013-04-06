<?php


class LineTest extends PHPUnit_Framework_TestCase
{

    public function testTrait()
    {
     
        $a=new A();
        $b=new B();
        
        $a->setA("this is a of A");
        $a->setB($b);
        
        $b->setA("This is a of B");
        
        $aStr=$a->faceGetter("b.a");
        var_dump($aStr);
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
    
    protected $a;

    
    public static function __getEntityFace() {
        return [
            
            "elements"=>[
                "a"=>[
                    "propertyName"=>"a",
                    "type"=>"value",
                ],
            ]
            
        ];
    }
    
    public function getA() {
        return $this->a;
    }

    public function setA($a) {
        $this->a = $a;
    }


    
}

?>
