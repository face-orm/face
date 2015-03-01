<?php

class ArrayLoaderTest extends PHPUnit_Framework_TestCase {

    public function testArrayLoader(){

        $faceLoader = new \Face\Core\FaceLoader();

        $faceLoader->addFace(new \Face\Core\EntityFace([
            "name"  => "testA",
            "class" => "A"
        ],$faceLoader));

        $faceLoader->addFace(new \Face\Core\EntityFace([
            "name"  => "testB",
            "class" => "B"
        ],$faceLoader));

        $arrayLoader = new \Face\Core\FaceLoader\ArrayLoader([
            [
                "name"  => "testA",
                "class" => "A"
            ],
            [
                "name"  => "testB",
                "class" => "B"
            ]
        ]);

        $this->assertEquals($faceLoader->getFaceForClass("A")->getName(),$arrayLoader->getFaceForClass("A")->getName());
        $this->assertEquals($faceLoader->getFaceForClass("A")->getClass(),$arrayLoader->getFaceForClass("A")->getClass());
        $this->assertEquals($faceLoader->getFaceForClass("B")->getName(),$arrayLoader->getFaceForClass("B")->getName());
        $this->assertEquals($faceLoader->getFaceForClass("B")->getClass(),$arrayLoader->getFaceForClass("B")->getClass());
        $this->assertEquals($faceLoader->getFaceForName("testA")->getName(),$arrayLoader->getFaceForName("testA")->getName());
        $this->assertEquals($faceLoader->getFaceForName("testA")->getClass(),$arrayLoader->getFaceForName("testA")->getClass());
        $this->assertEquals($faceLoader->getFaceForName("testB")->getName(),$arrayLoader->getFaceForName("testB")->getName());
        $this->assertEquals($faceLoader->getFaceForName("testB")->getClass(),$arrayLoader->getFaceForName("testB")->getClass());


    }

}
