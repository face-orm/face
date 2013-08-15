<?php

namespace Face\Sql\Query;

use Face\Core\EntityFace;
use Face\Core\Navigator;

/**
 * Description of Query
 *
 * @author Soufiane Ghzal
 */
abstract class  FQuery {

    protected $dotToken="__dot__"; // we have to reaplce the face navigation token "." by an other. indeed "." is not compatible with alias in sql and we want to avoid conflicts with user table / column names
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
     * 
     * @param \PDO $pdo
     */
    public function execute(\PDO $pdo){
        $stmt = $pdo->prepare($this->getSqlString());
        
        
        foreach($this->valueBinds as $name=>$bind){
            $stmt->bindValue($name, $bind[0], $bind[1]);
        }
        
        
        if($stmt->execute()){
            return $stmt;
            
        }else{
            echo "TODO  in file : ".__FILE__.":".__LINE__;
            var_dump($stmt->errorInfo());
            return false;
            
        }
        
        
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
        
        if("this"===$path || empty($path))
            return "this";
        
        if(0!==strncmp($path,"this.",5)) // if doens tbegin with "this." then we prepend "this."
            $path="this.".$path;
        
        return str_replace(".",$token,$path);
    }
    
    /**
     * 
     * @return EntityFace
     */
    public function getBaseFace() {
        return $this->baseFace;
    }


    
    
}