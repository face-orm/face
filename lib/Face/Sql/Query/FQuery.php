<?php

namespace Face\Sql\Query;

use Face\Config;
use Face\Core\EntityFace;
use Face\Core\EntityFaceElement;
use Face\Core\Navigator;
use Face\Exception\BadParameterException;
use Face\Exception\QueryFailedException;
use Face\Util\StringUtils;

/**
 * Description of Query
 *
 * @author Soufiane Ghzal
 */
abstract class FQuery
{


    // when we generate sql queries
    // we have to replace the face navigation token "." by an other
    // Because "." is not compatible with alias in sql and we want to avoid conflicts with user table / column names
    // then ___ is safe enough
    public static $DOT_TOKEN = ".";

    // alias that can be replaced from an instance
    protected $dotToken;

    
    
    /**
     *
     * @var EntityFace
     */
    protected $baseFace;
    
    /**
     * list of face joined to the query
     * @var array
     */
    protected $joins;




    /**
     * Only for select queries.
     * Contains the list of the selected columns formatted as follows :
     * $selectedColumns["alias-name"]="path"
     * @var array
     */
    protected $selectedColumns;

    /**
     * list of binds to pass to pdo object
     * @var array
     */
    protected $valueBinds;


    protected $aliases;

    function __construct(EntityFace $baseFace)
    {
        $this->dotToken = self::$DOT_TOKEN;
        $this->baseFace = $baseFace;
        $this->joins=[];
        $this->valueBinds=[];
    }

    
    abstract public function getSqlString();



    /**
     * Executes the query from the given pdo object
     * @param \PDO|Config|null $pdo
     * @return \PDOStatement the pdo statement ready to fetch
     */
    public function execute($config = null)
    {

        if(null == $config){
            $pdo = Config::getDefault()->getPdo();
        }else if($config instanceof \PDO){
            $pdo = $config;
        }else if($config instanceof Config){
            $pdo = $config->getPdo();
        }else{
            throw new BadParameterException('First parameter of FQuery::execute is not correct');
        }

        $stmt = $this->getPdoStatement($pdo);
        
        if ($stmt->execute()) {
            return $stmt;
            
        } else {
            // TODO : handle errors ".__FILE__.":".__LINE__;
            throw new QueryFailedException($stmt);
            //return false;
        }

    }

    /**
     * get a statement for the given pdo object, values are already bound
     * @param \PDO $pdo
     */
    public function getPdoStatement(\PDO $pdo)
    {
        $stmt = $pdo->prepare($this->getSqlString());


        foreach ($this->valueBinds as $name => $bind) {
            $stmt->bindValue($name, $bind[0], $bind[1]);
        }

        return $stmt;
    }
    
    public function bindValue($parameter, $value, $data_type = \PDO::PARAM_STR)
    {
        $this->valueBinds[$parameter]=[$value,$data_type];
        
        return $this;
    }
    
    public function getSelectedColumns()
    {
        return $this->selectedColumns;
    }


    /**
     * @param $name
     * @return array
     */
    public function getBoundValue($name)
    {
        return $this->valueBinds[$name];
    }

    /**
     * @param $name
     * @return array
     */
    public function getBoundValues()
    {
        return $this->valueBinds;
    }

    /**
     * give all the entities which are part of the FQuery
     * @return EntityFace[] list of the face
     */
    public function getAvailableFaces()
    {
        $array['this']=$this->baseFace;

        return array_merge($array, $this->joins);
    }
    
    /**
     * convention for having the same table alias every where. E.G  "a.b" will become "this__dot_a__dot__b"
     * @param string $path
     * @param string $token the token to use for separate elements of the path. Default  $this->getDotToken() will be used
     * @return string
     */
    public function _doFQLTableName($path, $token = null, $escape = false)
    {

        if (null===$token) {
            $token=$this->dotToken;
        }

        return self::__doFQLTableNameStatic($path, $token, $escape);
    }

    /**
     * reserved for internal usage
     * @param $path
     * @param null $token
     * @return mixed|string
     */
    public static function __doFQLTableNameStatic($path, $token = null, $escape = false)
    {
        if (null===$token) {
            $token=self::$DOT_TOKEN;
        }

        if ("this"===$path || empty($path)) {
            if($escape){
                return "`this`";
            }else{
                return "this";
            }
        }

        if (0!==strncmp($path, "this.", 5)) {
// if doesn't begin with "this." then we prepend "this."
            $path="this.".$path;
        }

        $name = str_replace(".", $token, $path);

        if($escape){
            return "`$name`";
        }else{
            return $name;
        }
    }

    /**
     * reserved for internal usage
     * @param $path path to the element
     * @param EntityFace $face the face that is joined
     * @param EntityFace $parentFace the other joined face
     * @param EntityFaceElement $childElement
     * @param $basePath
     * @param null $token
     * @return string
     */
    public static function __doFQLJoinTable($path, EntityFace $face, EntityFace $parentFace, EntityFaceElement $childElement, $basePath, $token, $isSoftJoin = false)
    {

        $relation = $childElement->getRelation();
        
        
        if ($relation == "hasManyThrough") {
            // How it is going to look :
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
            
            // How it is going to look :
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

                $joinSql.=" ".$parentJoin."=".$childJoin." ";

            }
            
        }

        return $joinSql;
    }

    /**
     * reserved for internal usage
     * @param array $columnList list of columns ["path"]="alias";
     * @param EntityFace $baseFace query baseface
     * @param string $token the token used for aliases. Null for using the default one
     * @return string list of columns sql formatted (comma separated) and aliased
     */
    public static function __doFQLSelectColumns(array $columnList, EntityFace $baseFace, $token = null)
    {

        $selectFields="";

        foreach ($columnList as $path => $alias) {
            try {
                $face=$baseFace->getElement($path, 1, $piecesOfPath)->getFace();

            } catch (\Face\Exception\RootFaceReachedException $e) {
                $piecesOfPath[0]="";
                $piecesOfPath[1]=$path;

                $face=$baseFace;
            }


            $selectFields.=self::__doFQLTableNameStatic($piecesOfPath[0], $token, true) .
                "." . $face->getElement($piecesOfPath[1])->getSqlColumnName(true) . " AS `" . $alias . "`,";

        }

        return rtrim($selectFields, ",");
    }

    /**
     *
     * @return EntityFace
     */
    public function getBaseFace()
    {
        return $this->baseFace;
    }
}
