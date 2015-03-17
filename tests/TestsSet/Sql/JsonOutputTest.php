<?php


class JsonOutputTest extends Test\PHPUnitTestDb
{


    /**
     * @group json
     */
    public function testSimpleSelect()
    {

        $pdo=$this->getConnection()->getConnection();

        $q=Tree::faceQueryBuilder();
        $trees=\Face\ORM::execute($q,$pdo);


        $actual = $trees->jsonSerialize();

        $expected = array (
            'items' =>
                array (
                    0 =>
                        array (
                            'id' => '1',
                            'age' => '8',
                        ),
                    1 =>
                        array (
                            'id' => '2',
                            'age' => '2',
                        ),
                    2 =>
                        array (
                            'id' => '3',
                            'age' => '5',
                        ),
                    3 =>
                        array (
                            'id' => '4',
                            'age' => '300',
                        ),
                ),
        );

        $this->assertEquals($expected, $actual);

    }





}
