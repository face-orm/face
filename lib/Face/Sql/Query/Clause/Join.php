<?php


namespace Face\Sql\Query\Clause;


use Face\Core\EntityFace;
use Face\Sql\Query\Clause\Select\Column;
use Face\Sql\Query\FQuery;
use Face\Sql\Query\SelectBuilder\JoinQueryFace;

class Join implements SqlClauseInterface {

    /**
     * @var EntityFace
     */
    protected $baseFace;

    /**
     * @var JoinQueryFace
     */
    protected $joinQueryFace;

    function __construct(EntityFace $baseFace, JoinQueryFace $joinQueryFace)
    {
        $this->baseFace = $baseFace;
        $this->joinQueryFace = $joinQueryFace;
    }


    public function getSqlString(FQuery $q)
    {
        $path = $this->joinQueryFace->getPath();
        $face = $this->joinQueryFace->getFace();
        try {
            $parentFace = $this->baseFace->getElement($path, 1, $pieceOfPath)->getFace();
        } catch (\Face\Exception\RootFaceReachedException $e) {
            $pieceOfPath[0] = "";
            $pieceOfPath[1] = $path;
            $parentFace = $this->baseFace;
        }
        $childElement = $parentFace->getElement($pieceOfPath[1]);
        $basePath = $pieceOfPath[0];
        $token = ".";
        $isSoftJoin = $this->joinQueryFace->isSoft();


        $relation = $childElement->getRelation();

        $joinSql = "";

        if ($relation == "hasManyThrough") {
            // Final render :
            // JOIN throughTable AS throughtAlias
            //      ON throughtAlias.one = parent.one AND throughtAlias.two = parent.two
            // JOIN otherTable AS otherAlias
            //      ON otherAlias.one = throughtAlias.one AND otherAlias.two = throughtAlias.two

            $throughTable = $childElement->getSqlThrough();
            $throughAlias = FQuery::__doFQLTableNameStatic("$path.through", $token);

            $joinSql1 = "LEFT JOIN `$throughTable` AS `$throughAlias` ON ";

            $join = $childElement->getSqlJoin();
            $i=0;

            foreach ($join as $thisElementName => $throughcolumn) {
                if ($i>0) {
                    $joinSql1.=" AND ";
                } else {
                    $i++;
                }

                $parentOn  = FQuery::__doFQLTableNameStatic($basePath, $token, true).".".$parentFace->getElement($thisElementName)->getSqlColumnName(true);
                $throughOn = "`$throughAlias`.`$throughcolumn`";
                $joinSql1 .= "$parentOn = $throughOn" ;
            }

            // In a soft join we dont join the other table, only the through table
            if (false === $isSoftJoin) {
                $otherFace    = $face;
                $otherTableElement = $otherFace->getDirectElement($childElement->getRelatedBy());

                $otherTable        = $otherFace->getSqlTable(true);
                $otherAlias        = FQuery::__doFQLTableNameStatic($path, $token, true);

                $joinSql2 = "LEFT JOIN $otherTable AS $otherAlias ON ";
                $join = $otherTableElement->getSqlJoin();
                $i = 0;
                foreach ($join as $thisElementName => $throughcolumn) {
                    if ($i > 0) {
                        $joinSql2 .= " AND ";
                    } else {
                        $i++;
                    }

                    $otherOn = "$otherAlias." . $otherFace->getElement($thisElementName)->getSqlColumnName(true);
                    $throughOn = "`$throughAlias`.`$throughcolumn`";
                    $joinSql2 .= "$otherOn = $throughOn";
                }

            } else {
                $joinSql2 = "";

            }

            $joinSql = "$joinSql1 $joinSql2";


        } else {
            $joinArray = $childElement->getSqlJoin();

            // Final render
            // JOIN something AS alias ON alias.one = parent.one AND alias.two = parent.two

            // Begining of the join clause
            // JOIN something AS alias ON
            $joinSql = "LEFT JOIN " . $face->getSqlTable(true) . " AS " . FQuery::__doFQLTableNameStatic($path, $token, true) . " ON";

            //end of the join clause
            // alias.one = parent.one AND alias.two = parent.two
            $i=0;
            foreach ($joinArray as $parentJoinElementName => $childJoinElementName) {
                $parentJoin = FQuery::__doFQLTableNameStatic($basePath, $token, true) . '.' . $parentFace->getElement($parentJoinElementName)->getSqlColumnName(true);
                $childJoin  = FQuery::__doFQLTableNameStatic($path, $token, true) . '.' . $childElement->getFace()->getElement($childJoinElementName)->getSqlColumnName(true);

                if ($i>0) {
                    $joinSql.=" AND ";
                } else {
                    $i++;
                }

                $joinSql.=" ".$parentJoin." = ".$childJoin." ";

            }

        }

        return rtrim($joinSql);


    }


}