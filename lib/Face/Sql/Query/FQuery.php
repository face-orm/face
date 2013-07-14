<?php

namespace Face\Sql\Query;

use Face\Core\EntityFace;
use Face\Core\Navigator;

/**
 * Description of Query
 *
 * @author Soufiane Ghzal
 */
class FQuery {
    
    protected $dotToken="__dot__"; // we have to reaplce the face navigation token "." by an other because "." is not compatible with alias in sql and we want to avoid conflicts with table / column names
    
    /**
     *
     * @var EntityFace
     */
    protected $baseFace;
    
    /**
     *
     * @var array
     */
    protected $joins;
    
    protected $where;
    
    protected $valueBinds;
    
    function __construct(EntityFace $baseFace) {
        $this->baseFace = $baseFace;
        $this->joins=[];
        $this->valueBinds=[];
    }
    
    /**
     * add a join clause to the query
     * @param string $path the face path to the join
     * @return \FaceSql\Query\FQuery  will return $this (fluent method)
     */
    public function join($path){
        $this->joins[$this->_doFQLTableName($path, ".")]=$this->baseFace->getElement($path)->getFace();
        
        return $this;
    }
    
    public function bindValue($parameter, $value,  $data_type = \PDO::PARAM_STR  ){
        $this->valueBinds[]=[$parameter,$value,$data_type];
        
        return $this;
    }
    
    /**
     * give all the entities which are part of the FQuery
     * @return EntityFace[] list of the face
     */
    public function getAvailableFaces(){
        $array['this']=$this->baseFace;
        
        return array_merge($array,$this->joins);
    }
    
    /**
     * set the where clause 
     * @param string $whereString the FQuery formated  where clause
     * @return \FaceSql\Query\FQuery  will return $this (fluent method)
     */
    public function where($whereString){
        $this->where=$whereString;
        
        return $this;
    }
    
    /**
     * 
     * @param \PDO $pdo
     */
    public function execute($pdo){
        $stmt = $pdo->prepare($this->getSqlString());
        
        foreach($this->valueBinds as $bind){
            $stmt->bindValue($bind[0], $bind[1], $bind[2]);
        }
        
        
        if($stmt->execute()){
            
            return $stmt;
            
        }else{
            echo "TODO : ".__FILE__.":".__LINE__;
            var_dump($stmt->errorInfo());
            return false;
            
        }
        
        
    }
    
    public function getSqlString(){
        
        $sqlQ=$this->prepareSelectClause();
        $sqlQ.=" ".$this->prepareFromClause();
        $sqlQ.=" ".$this->prepareJoinClause();
        $sqlQ.=" ".$this->prepareWhereClause();
        
       
        return $sqlQ;
        
    }
    
    public function prepareSelectClause(){
        
        
        $facesToSelect["this"]=$this->baseFace;  
        $facesToSelect=  array_merge($facesToSelect,$this->joins);
        
        $selectFields=[];
        
        foreach($facesToSelect as $path=>$face){
            $truePath = $this->_doFQLTableName($path);
            foreach($face as $elm){
                /* @var $elm \Face\Core\EntityFaceElement */
                if($elm->isValue())
                    $selectFields[]=$truePath.".".$elm->getSqlColumnName()." AS ".$truePath.$this->dotToken.$elm->getName();
            }
        }
        
        $sql="SELECT ";
        $sql.=implode(",", $selectFields);
        
        
        return $sql;
    }
    
    public function prepareFromClause(){
        
                
        return "FROM ".$this->baseFace->getSqlTable()." AS this";
        
    }
    
    public function prepareJoinClause(){
        
        $sql="";
        
        foreach ($this->joins as $path=>$face){
            /* @var $face EntityFace */
            
            
            try{
                $pieceOfPath;
                $parentFace=$this->baseFace->getElement($path,1,$pieceOfPath)->getFace();
            } catch (\Face\Exception\RootFaceReachedException $e){
                $pieceOfPath[0]="";
                $pieceOfPath[1]=$path;
                $parentFace=$this->baseFace;
            }
            
            $childElement=$parentFace->getElement($pieceOfPath[1]);
            
            
            // Begining of the join clause
            // JOIN something AS alias ON 
            $joinSql="LEFT JOIN ".$face->getSqlTable()." AS ".$this->_doFQLTableName($path)." ON ";
            
            
            $joinArray=$childElement->getSqlJoin();
            
            //end of the join clause
            // alias.one = parent.one AND alias.two = parent.two
            $i=0;
            foreach($joinArray as $parentJoinElementName=>$childJoinElementName){
                $parentJoin=$this->_doFQLTableName($pieceOfPath[0]).".".$parentFace->getElement($parentJoinElementName)->getSqlColumnName();
                $childJoin=$this->_doFQLTableName($path).".".$childElement->getFace()->getElement($childJoinElementName)->getSqlColumnName();
                
                if($i>0)
                    $joinSql.=" AND ";
                else
                    $i++;
                
                $joinSql.=" ".$parentJoin."=".$childJoin." ";
                
            }
            
            
            
            $sql.=$joinSql;
        }

        return $sql;
    }
    
    public function prepareWhereClause(){
        if(null===$this->where || empty($this->where))
            return "";
        
        
        $newString=$this->where;
        
        $matchArray = [];
        preg_match_all("#~([a-zA-Z0-9_]\.{0,1})+#", $newString,$matchArray);
        $matchArray = array_unique($matchArray[0]);
        
        foreach ($matchArray as $match) {
            
            $path=ltrim($match,"~");
            
            $replace=$this->_doFQLTableName(substr($match,1, strrpos($match,".")))
                        .".".$this->baseFace->getElement($path)->getSqlColumnName();
            
            $newString=str_replace($match, $replace, $newString);
            
        }
        
        
        return "WHERE ".$newString;
        
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
    

    

    
}

?>
