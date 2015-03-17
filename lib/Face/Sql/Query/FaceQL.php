<?php

namespace Face\Sql\Query;

use Face\Core\EntityFace;
use Face\Core\Navigator;
use Face\Exception\FacelessException;
use Face\Util\OOPUtils;

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
abstract class FaceQL
{

    const PATTERN_FROM="#FROM::(\\\\?[A-Za-z_][0-9A-Za-z_]*(\\\\[A-Za-z_][0-9A-Za-z_]*)*)#";
    const PATTERN_SELECT="#SELECT::((\\[.+\\])|\\*)#";
    const PATTERN_JOIN="#JOIN::([a-zA-Z_][a-zA-Z0-9_]*(\\.[a-zA-Z_][a-zA-Z0-9_]*)*)#";
    const PATTERN_DYNCOLUMN="#~([a-zA-Z0-9_]\\.{0,1})+#";
    const PATTERN_SUBQUERY="#SUB::#";


    /**
     * parse an FaceQL statement and return an executable QueryString object
     * @param $string
     * @return QueryString
     */
    public static function parse($string)
    {

        $baseString=$string;

        /*
         * FROM Clause
         */
        $baseFace=self::_parseFromClause($string);

        /*
         * JOIN Clauses
         */
        $joinFaces=self::_parseJoinFace($string, $baseFace);

        /*
         * SELECT Clause
         */
        $selectedColumns=self::_parseSelectClause($string, $baseFace, $joinFaces);

        /*
         * Dynamic Columns
         */
        self::_parseDynColumns($string, $baseFace);


        return new QueryString($baseFace, $string, $joinFaces, $selectedColumns);

    }


    private static function _parseDynColumns(&$string, EntityFace $baseFace)
    {

        preg_match_all(self::PATTERN_DYNCOLUMN, $string, $matchArray);
        $matchArray = array_unique($matchArray[0]);

        foreach ($matchArray as $match) {
            $path=ltrim($match, "~");

            $tablePath = rtrim(substr($match, 1, strrpos($match, ".")), ".");

            $replace=FQuery::__doFQLTableNameStatic($tablePath, null, true)
                .".".$baseFace->getElement($path)->getSqlColumnName(true);

            $string=str_replace($match, $replace, $string);

        }
    }

    private static function _parseSelectClause(&$string, EntityFace $baseFace, array $joins)
    {

        $match=[];
        if (preg_match(self::PATTERN_SELECT, $string, $match)!=1) {
            throw new \Exception("Problem occurred while parsing the SELECT clause");
        }

        $matchString=$match[1];

        // list of base face + join face
        $facesToSelect["this"]=$baseFace;
        $facesToSelect = array_merge($facesToSelect, $joins);

        // the STAR [*] case : select all
        // TODO : More flexible match
        if ($matchString == "*" || $matchString == "[*]") {
            $selectFields=[];

            foreach ($facesToSelect as $path => $face) {
                $truePath = FQuery::__doFQLTableNameStatic($path);
                foreach ($face as $elm) {
                    /* @var $elm \Face\Core\EntityFaceElement */
                    if ($elm->isValue()) {
                        $selectFields[ $path . "." .  $elm->getName() ]
                            = $truePath . FQuery::$DOT_TOKEN . $elm->getName();
                    }
                }
            }

        } else {
            // TODO
        }


        $sqlSelect = FQuery::__doFQLSelectColumns($selectFields, $baseFace);



        $string=str_replace(
            $match[0],
            " SELECT " . $sqlSelect,
            $string
        );

        return $selectFields;


    }


    private static function _parseFromClause(&$string)
    {
        $match=[];
        if (preg_match(self::PATTERN_FROM, $string, $match)!=1) {
            throw new \Exception("Problem occurred while parsing the FROM clause");
        }

        $classname=isset($match[1])?$match[1]:"";

        if (!class_exists($classname)) {
            throw new \Exception("Class '$classname' does not exist");
        }

        if (!OOPUtils::UsesTrait($classname, 'Face\Traits\EntityFaceTrait')) {
            throw new FacelessException("Class '$classname' does not use EntityFaceTrait");
        }

        /** @var  $face EntityFace */
        $face = call_user_func($classname .'::getEntityFace');

        $string=str_replace(
            $match[0],
            " FROM " . $face->getSqlTable(true) . " AS " . FQuery::__doFQLTableNameStatic("this", null, true),
            $string
        );

        return $face;
    }

    private static function _parseJoinFace(&$string, EntityFace $baseFace)
    {
        $matches=[];
        $parseResult=preg_match_all(self::PATTERN_JOIN, $string, $matches);

        if ($parseResult===false) {
            throw new \Exception("Problem occurred while parsing the JOIN clauses");
        }

        $matchesFaceIndex=1;
        $matchesReplaceIndex=0;

        if ($parseResult===0 || count($matches)==0) {
            return [];
        }


        $returns=[];
        foreach ($matches[$matchesFaceIndex] as $k => $match) {
            try {
                $path = $match;

                $faceElement = $baseFace->getElement($path);
                $face = $faceElement->getFace();

                $returns[FQuery::__doFQLTableNameStatic($matches[$matchesFaceIndex][$k], ".")]=$face;


                try {
                    $parentFace=$baseFace->getElement($path, 1, $pieceOfPath)->getFace();
                } catch (\Face\Exception\RootFaceReachedException $e) {
                    $pieceOfPath[0]="";
                    $pieceOfPath[1]=$path;
                    $parentFace=$baseFace;
                }

                $childElement=$parentFace->getElement($pieceOfPath[1]);



                $joinSql = FQuery::__doFQLJoinTable($path, $face, $parentFace, $childElement, $pieceOfPath[0], null);


                $pos = strpos($string, $matches[$matchesReplaceIndex][$k]);
                $string = substr_replace($string, $joinSql, $pos, strlen($matches[$matchesReplaceIndex][$k]));

//
//                $string=str_replace(
//                    $matches[$matchesReplaceIndex][$k]
//                    ,$joinSql
//                    ,$string
//                );


            } catch (\Exception $e) {
                throw $e;
            }
        }

        return $returns;
    }
}
