<?php

class FaceLoaderTest extends PHPUnit_Framework_TestCase {

    public function testFaceLoader(){

        $expectedA = new \Face\Core\EntityFace([
            "name"  => "testA",
            "class" => "A"
        ]);

        $expectedB = new \Face\Core\EntityFace([
            "name"  => "testB",
            "class" => "B"
        ]);

        $faceLoader = new \Face\Core\FaceLoader();

        $faceLoader->addFace($expectedA);
        $faceLoader->addFace($expectedB);

        $this->assertTrue($faceLoader->faceClassExists("A"));
        $this->assertTrue($faceLoader->faceClassExists("B"));
        $this->assertFalse($faceLoader->faceClassExists("C"));
        $this->assertFalse($faceLoader->faceClassExists("testA"));
        $this->assertFalse($faceLoader->faceClassExists("testB"));

        $this->assertTrue($faceLoader->faceNameExists("testA"));
        $this->assertTrue($faceLoader->faceNameExists("testB"));
        $this->assertFalse($faceLoader->faceNameExists("testC"));
        $this->assertFalse($faceLoader->faceNameExists("A"));
        $this->assertFalse($faceLoader->faceNameExists("B"));


        $this->assertSame($expectedA,$faceLoader->getFaceForClass("A"));
        $this->assertSame($expectedB,$faceLoader->getFaceForClass("B"));

        $this->assertSame($expectedA,$faceLoader->getFaceForName("testA"));
        $this->assertSame($expectedB,$faceLoader->getFaceForName("testB"));

        try{
            $faceLoader->getFaceForName("A");
            $this->fail("Eception expected");
        }catch(\Face\Exception\FaceNameDoesntExistsException $e){
            $this->assertInstanceOf("\\Face\\Exception\\FaceNameDoesntExistsException", $e);
        }

        try{
            $faceLoader->getFaceForName("B");
            $this->fail("Eception expected");
        }catch(\Face\Exception\FaceNameDoesntExistsException $e){
            $this->assertInstanceOf("\\Face\\Exception\\FaceNameDoesntExistsException", $e);
        }

        try{
            $faceLoader->getFaceForName("C");
            $this->fail("Eception expected");
        }catch(\Face\Exception\FaceNameDoesntExistsException $e){
            $this->assertInstanceOf("\\Face\\Exception\\FaceNameDoesntExistsException", $e);
        }



        try{
            $faceLoader->getFaceForClass("testA");
            $this->fail("Exception expected");
        }catch(\Face\Exception\FaceClassDoesntExistsException $e){
            $this->assertInstanceOf("\\Face\\Exception\\FaceClassDoesntExistsException", $e);
        }

        try{
            $faceLoader->getFaceForClass("testB");
            $this->fail("Exception expected");
        }catch(\Face\Exception\FaceClassDoesntExistsException $e){
            $this->assertInstanceOf("\\Face\\Exception\\FaceClassDoesntExistsException", $e);
        }

        try{
            $faceLoader->getFaceForClass("testC");
            $this->fail("Exception expected");
        }catch(\Face\Exception\FaceClassDoesntExistsException $e){
            $this->assertInstanceOf("\\Face\\Exception\\FaceClassDoesntExistsException", $e);
        }



    }

}
