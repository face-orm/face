<?php

namespace Test;


use Face\Core\EntityFaceElement;
use Face\Sql\Query\SelectBuilder;

class MockSelectBuilder extends SelectBuilder {

    protected $tests;

    public function whereINRelation($tests,$r,$e,$l=true){
        $this->tests = $tests;
        parent::whereINRelation($r,$e,$l);

    }

    protected function __whereINRelationOneIdentifierGetValues($entities,$itsColumn,EntityFaceElement $relatedElement){

        $values = parent::__whereINRelationOneIdentifierGetValues($entities,$itsColumn, $relatedElement);

        foreach($this->tests as $t){

            call_user_func($t,$values);

        }

        return $values;

    }

}