<?php

namespace Face\Sql\Query;

use Face\Core\EntityFace;
use Face\Core\EntityFaceElement;
use Face\Core\Navigator;

/**
 * Description of Query
 *
 * @author Soufiane Ghzal
 */
abstract class  FQuery {

    const DOT_TOKEN="__dot__";

    protected $dotToken=self::DOT_TOKEN; // we have to replace the face navigation token "." by an other. indeed "." is not compatible with alias in sql and we want to avoid conflicts with user table / column names
    // then __dot__ is safe enough
    
    
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
     * list of binds to pass to pdo object
     * @var array
     */
    protected $valueBinds;
    
    function __construct(EntityFace $baseFace) {
        $this->baseFace = $baseFace;
        $this->joins=[];
        $this->valueBinds=[];
    }

    
    public abstract function getSqlString();






    /**
     * Executes the query from the given pdo object
     * @param \PDO $pdo
     */
    public function execute(\PDO $pdo){

        $stmt = $this->getPdoStatement($pdo);
        
        if($stmt->execute()){
            return $stmt;
            
        }else{
            echo "TODO  in file : ".__FILE__.":".__LINE__;
            var_dump($stmt->errorInfo());
            return false;
            
        }

    }

    /**
     * get a statement for the given pdo object, values are already bound
     * @param \PDO $pdo
     */
    public function getPdoStatement(\PDO $pdo){
        $stmt = $pdo->prepare($this->getSqlString());


        foreach($this->valueBinds as $name=>$bind){
            $stmt->bindValue($name, $bind[0], $bind[1]);
        }

        return $stmt;
    }
    
    public function bindValue($parameter, $value,  $data_type = \PDO::PARAM_STR  ){
        $this->valueBinds[$parameter]=[$value,$data_type];
        
        return $this;
    }

    /**
     * @param $name
     * @return array
     */
    public function getBoundValue($name){
        return $this->valueBinds[$name];
    }
    
    /**
     * convention for having the same table alias every where. E.G  "a.b" will become "this__dot_a__dot__b"
     * @param string $path
     * @param string $token the token to use for separate elements of the path. Default  $this->getDotToken() will be used
     * @return string
     */
    public function _doFQLTableName($path,$token=null){
        
        if(null===$token )
            $token=$this->dotToken;
        
        return self::__doFQLTableNameStatic($path,$token);
    }

    /**
     * reserved for internal usage
     * @param $path
     * @param null $token
     * @return mixed|string
     */
    public static function __doFQLTableNameStatic($path,$token=null){
        if(null===$token )
            $token=self::DOT_TOKEN;

        if("this"===$path || empty($path))
            return "this";

        if(0!==strncmp($path,"this.",5)) // if doesn't begin with "this." then we prepend "this."
        $path="this.".$path;

        return str_replace(".",$token,$path);
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
    public static function __doFQLJoinTable($path,EntityFace $face,EntityFace $parentFace,EntityFaceElement $childElement,$basePath,$token=null){

        // Begining of the join clause
        // JOIN something AS alias ON
        $joinSql="LEFT JOIN ".$face->getSqlTable()." AS ".FQuery::__doFQLTableNameStatic($path,$token)." ON ";


        $joinArray=$childElement->getSqlJoin();

        //end of the join clause
        // alias.one = parent.one AND alias.two = parent.two
        $i=0;
        foreach($joinArray as $parentJoinElementName=>$childJoinElementName){
            $parentJoin=FQuery::__doFQLTableNameStatic($basePath,$token).".".$parentFace->getElement($parentJoinElementName)->getSqlColumnName();
            $childJoin=FQuery::__doFQLTableNameStatic($path,$token).".".$childElement->getFace()->getElement($childJoinElementName)->getSqlColumnName();

            if($i>0)
                $joinSql.=" AND ";
            else
                $i++;

            $joinSql.=" ".$parentJoin."=".$childJoin." ";

        }

        return $joinSql;
    }

    /**
     * 
     * @return EntityFace
     */
    public function getBaseFace() {
        return $this->baseFace;
    }
    
}