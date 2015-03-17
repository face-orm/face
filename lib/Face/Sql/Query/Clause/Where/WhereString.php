<?php

namespace Face\Sql\Query\Clause\Where;

use Face\Sql\Query\Clause\Where;
use Face\Sql\Query\Clause\WhereInterface;
use Face\Sql\Query\FQuery;
use Face\Traits\ContextAwareTrait;

class WhereString implements WhereInterface
{

    use ContextAwareTrait;

    protected $string;

    function __construct($string)
    {
        $this->string = $string;
    }

    public function getSqlString(FQuery $q)
    {

        $newString=$this->string;

        $matchArray = [];
        preg_match_all("#~([a-zA-Z0-9_]\\.{0,1})+#", $newString, $matchArray);
        $matchArray = array_unique($matchArray[0]);

        foreach ($matchArray as $match) {
            $nsMatch=$this->getNameInContext($match);

            $path=ltrim($nsMatch, "~");

            $tablePath = rtrim(substr($nsMatch, 1, strrpos($nsMatch, ".")), ".");

            $replace= $q->_doFQLTableName($tablePath, null, true)
                 . "." . $q->getBaseFace()->getElement($path)->getSqlColumnName(true);

            $newString=str_replace($match, $replace, $newString);

        }

        return $newString;

    }
}
