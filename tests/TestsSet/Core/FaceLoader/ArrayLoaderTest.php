<?php

class ArrayLoaderTest extends PHPUnit_Framework_TestCase {

    public function testArrayLoader(){

        $faceLoader = new \Face\Core\FaceLoader();

        $faceLoader->addFace(new \Face\Core\EntityFace([
            "name"  => "testA",
            "class" => "A"
        ]));

        $faceLoader->addFace(new \Face\Core\EntityFace([
            "name"  => "testB",
            "class" => "B"
        ]));

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

        $this->assertEquals($faceLoader->getFaceForClass("A"),$arrayLoader->getFaceForClass("A"));
        $this->assertEquals($faceLoader->getFaceForClass("B"),$arrayLoader->getFaceForClass("B"));
        $this->assertEquals($faceLoader->getFaceForName("testA"),$arrayLoader->getFaceForName("testA"));
        $this->assertEquals($faceLoader->getFaceForName("testB"),$arrayLoader->getFaceForName("testB"));


    }

}
