<?php



class WhereTest extends PHPUnit_Framework_TestCase
{

    public function getFQuery(){
        return new \Face\Sql\Query\SelectBuilder(Tree::getEntityFace());
    }


    public function testWhereString(){

        $w = new \Face\Sql\Query\Clause\Where\WhereString("a='b'");
        $this->assertEquals("a='b'",$w->getSqlString($this->getFQuery()));

        $fQuery = $this->getFQuery();
        $dotToken = $fQuery::DOT_TOKEN;

        $w = new \Face\Sql\Query\Clause\Where\WhereString("~lemons.id=5");
        $this->assertEquals("this${dotToken}lemons.id=5",$w->getSqlString($this->getFQuery()));

        $w = new \Face\Sql\Query\Clause\Where\WhereString("~seeds.id=5");
        $w->context("lemons");
        $this->assertEquals("this${dotToken}lemons${dotToken}seeds.id=5",$w->getSqlString($this->getFQuery()));

    }

    public function testWhereGroup(){

        $w = new \Face\Sql\Query\Clause\Where\WhereGroup();
        $this->assertEmpty($w->getSqlString($this->getFQuery()));

        $sWhere = new \Face\Sql\Query\Clause\Where\WhereString("a='b'");
        $w->addWhere($sWhere);
        $this->assertEquals("(a='b')",$w->getSqlString($this->getFQuery()));

        $sWhere = new \Face\Sql\Query\Clause\Where\WhereString("c='d'");
        $w->addWhere($sWhere);
        $this->assertEquals("(a='b' AND c='d')",$w->getSqlString($this->getFQuery()));

        $sWhere = new \Face\Sql\Query\Clause\Where\WhereString("e='f'");
        $w->addWhere($sWhere,"OR");
        $this->assertEquals("(a='b' AND c='d' OR e='f')",$w->getSqlString($this->getFQuery()));

        $sWhere = new \Face\Sql\Query\Clause\Where\WhereGroup();
        $sWhere->addWhere( new \Face\Sql\Query\Clause\Where\WhereString("g='h'"));
        $sWhere->addWhere( new \Face\Sql\Query\Clause\Where\WhereString("i='j'"),"OR");
        $w->addWhere($sWhere);
        $this->assertEquals("(a='b' AND c='d' OR e='f' AND (g='h' OR i='j'))",$w->getSqlString($this->getFQuery()));


    }


}
