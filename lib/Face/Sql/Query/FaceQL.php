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
abstract class FaceQL  {

    const PATTERN_FROM="#FROM::(\\\\?[A-Za-z_][0-9A-Za-z_]*(\\\\[A-Za-z_][0-9A-Za-z_]*)*)#";
    const PATTERN_SELECT="#SELECT::((\\[.+\\])|\\*)#";
    const PATTERN_JOIN="#JOIN::([a-zA-Z_][a-zA-Z0-9_]*(\\.[a-zA-Z_][a-zA-Z0-9_]*)*)#";
    const PATTERN_SUBQUERY="#SUB::#";


    public static function parse($string)
    {

        $baseString=$string;

        /*
         * FROM CLAUSE
         */
        $baseFace=self::_parseFromClause($string);

        $joinFaces=self::_parseJoinFace($string,$baseFace);

        var_dump($string);

    }


    private static function _parseFromClause(&$string){
        $match=[];
        if(preg_match(self::PATTERN_FROM,$string,$match)!=1)
            throw new \Exception("Problem occurred while parsing the FROM clause");

        $classname=isset($match[1])?$match[1]:"";

        if(!class_exists($classname))
            throw new \Exception("Class '$classname' does not exist");

        if(!OOPUtils::UsesTrait($classname,'Face\Traits\EntityFaceTrait'))
            throw new FacelessException("Class '$classname' does not use EntityFaceTrait");

        $face = call_user_func($classname .'::getEntityFace');

        $string=str_replace(
            $match[0]
           ," FROM " . $face->getSqlTable() . " AS " . FQuery::__doFQLTableNameStatic("this")
           ,$string
        );

        return $face;
    }

    private static function _parseJoinFace(&$string,EntityFace $baseFace){
        $matches=[];
        $parseResult=preg_match_all(self::PATTERN_JOIN,$string,$matches);

        if($parseResult===false)
            throw new \Exception("Problem occurred while parsing the JOIN clauses");

        $matchesFaceIndex=1;
        $matchesReplaceIndex=0;

        if($parseResult===0 || count($matches)==0)
            return [];


        $returns=[];
        foreach($matches[$matchesFaceIndex] as $k=>$match){
            try{



                $path = $match;

                $faceElement = $baseFace->getElement($path);
                $face = $faceElement->getFace();

                $returns[FQuery::__doFQLTableNameStatic($matches[$matchesFaceIndex][$k] , ".")]=$face;


                try{
                    $pieceOfPath;
                    $parentFace=$baseFace->getElement($path,1,$pieceOfPath)->getFace();
                } catch (\Face\Exception\RootFaceReachedException $e){
                    $pieceOfPath[0]="";
                    $pieceOfPath[1]=$path;
                    $parentFace=$baseFace;
                }

                $childElement=$parentFace->getElement($pieceOfPath[1]);


                $joinSql = FQuery::__doFQLJoinTable($path,$face,$parentFace,$childElement,$pieceOfPath[0]);


                $string=str_replace(
                    $matches[$matchesReplaceIndex][$k]
                    ,$joinSql
                    ,$string
                );

            }catch (\Exception $e){
                throw $e;
            }
        }

        return $returns;
    }



}