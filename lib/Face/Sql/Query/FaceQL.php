<?php

namespace Face\Sql\Query;

use Face\Core\EntityFace;
use Face\Core\Navigator;
use Face\Exception\FacelessException;
use Peek\Utils\OOPUtils;
use Peek\Utils\StringUtils;

/**
 * FaceQL is a SQL like language made for Face
 *
 * Example of a faceQL Query
 *
 * SELECT::[*,!lemons.mature]
 * FROM::Tree
 *  JOIN::lemons
 *  JOIN::lemons.seeds
 * WHERE
 *  ~age>:age
 *  ~id IN ( SUB::mySubQuery )
 * GROUP BY ~age
 * HAVING count(~lemon.id)>5
 *
 * @author sghzal
 */
class FaceQL extends FQuery {

    const PATTERN_FROM="#FROM::(\\\\?[A-Za-z_][0-9A-Za-z_]*(\\\\[A-Za-z_][0-9A-Za-z_]*)*)#";
    const PATTERN_SELECT="#SELECT::((\\[.+\\])|\\*)#";
    const PATTERN_JOIN="#JOIN::([a-zA-Z_][a-zA-Z0-9_]*(\\.[a-zA-Z_][a-zA-Z0-9_]*)*)#";
    const PATTERN_SUBQUERY="#SUB::#";

    protected $baseString;

    function __construct($string)
    {

        $this->baseString=$string;

        $baseFace=$this->_parseBaseFace();

        parent::__construct($baseFace);


    }


    protected function _parseBaseFace(){
        $match=[];
        if(preg_match(self::PATTERN_FROM,$this->baseString,$match)!=1)
            throw new \Exception("Problem occurred while parsing the FROM clause");

        $classname=isset($match[1])?$match[1]:"";
        if(!class_exists($classname))
            throw new \Exception("Class '$classname' does not exist");

        if(!OOPUtils::UsesTrait($classname,'Face\Traits\EntityFaceTrait'))
            throw new FacelessException("Class '$classname' does not use EntityFaceTrait");

        $face=call_user_func($classname .'::getEntityFace');

        return $face;
    }

    protected function _parseJoinFace(){
        $matches=[];
        $parseResult=preg_match_all(self::PATTERN_JOIN,$this->baseString,$matches);

        if($parseResult===false)
            throw new \Exception("Problem occurred while parsing the JOIN clauses");

        $matches=$matches[1];

        if($parseResult===0 || count($matches)===0)
            return [];

        $returns=[];
        foreach($matches as $match){
            try{
                $returns[]=$this->getBaseFace()->getElement($match);
            }catch (\Exception $e){
                throw $e;
            }
        }

        return $returns;
    }

    public function getSqlString()
    {

    }


}