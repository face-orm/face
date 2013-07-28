<?php

namespace Face\Sql\Query;

use Face\Core\EntityFace;

/**
 * Description of QueryBuilder
 *
 * @author bobito
 */
class SelectBuilder extends \Face\Sql\Query\FQuery{
       
    /** 
     *
     * @var string the base FaceQL formated where clause 
     */
    protected $where;
    
    function __construct(EntityFace $baseFace) {
        parent::__construct($baseFace);
    }

    
    public function getSqlString(){
        
        $sqlQ=$this->prepareSelectClause();
        $sqlQ.=" ".$this->prepareFromClause();
        $sqlQ.=" ".$this->prepareJoinClause();
        $sqlQ.=" ".$this->prepareWhereClause();
        
       
        return $sqlQ;
        
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
            
            $tablePath = rtrim(substr($match,1, strrpos($match,".")),".");
            
            $replace=$this->_doFQLTableName( $tablePath )
                        .".".$this->baseFace->getElement($path)->getSqlColumnName();
            
            $newString=str_replace($match, $replace, $newString);
            
        }
        
        
        return "WHERE ".$newString;
        
    }
    

    

    

    
}