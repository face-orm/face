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

    /**
     * used to generate unique bound params for whereIn method
     * @var int
     */
    private $whereInCount=0;
    
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
     * @return \Face\Sql\Query\SelectBuilder it returns itSelf
     */
    public function join($path){
        $this->joins[$this->_doFQLTableName($path, ".")]=$this->baseFace->getElement($path)->getFace();
        
        return $this;
    }

    
    

    
    /**
     * set the where clause 
     * @param string $whereString the FQuery formated  where clause
     * @return \Face\Sql\Query\SelectBuilder   it returns itSelf
     */
    public function where($whereString){
        $this->where=$whereString;
        
        return $this;
    }

    /**
     * appends an AND part to the current where clause
     * @param string $whereString the FQuery formated  where clause
     * @return \Face\Sql\Query\SelectBuilder   it returns itSelf
     */
    public function whereAND($whereString){
        $this->where.=" AND " . $whereString;
        return $this;
    }

    /**
     * appends an OR part to the current where clause
     * @param string $whereString the FQuery formated  where clause
     * @return \Face\Sql\Query\SelectBuilder   it returns itSelf
     */
    public function whereOR($whereString){
        $this->where.=" OR " . $whereString;
        return $this;
    }


    /**
     * creates Where $fieldName in (:$array[0],:$array[1],...)
     * and generates some bindValue
     * @param $fieldName string name of the column (can make use of dynamic creation with ~dynColName)
     * @param $array array list of values to bind
     * @return \Face\Sql\Query\SelectBuilder  it returns itSelf
     */
    public function whereIN($fieldName,$array,$logic=true){
        $bindString = "";
        foreach($array as $value){
            $bindString.=',:fautoIn'.++$this->whereInCount;
            $this->bindValue(':fautoIn'.$this->whereInCount,$value);
        }

        $phrase = $fieldName . " IN (" . ltrim($bindString,",") . ")";
        if      ("AND" === $logic)  //user asked for AND
            return $this->whereAND($phrase);
        else if ("OR" === $logic)  //user asked for Or
            return $this->whereOR($phrase);
        else if (true == $logic){ // auto check whether logic is needed
            if(null !== $this->where || strlen($this->where)>0)
                return $this->whereAND($phrase);
            else
                return $this->where($phrase);
        }else if(false == $logic) // user asked for no logic
            return $this->where($phrase);
        else
            throw new \Exception("Unrecognized logic expression for third param. 'OR' or 'AND' or true or false is awaiting");
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
                $parentFace=$this->baseFace->getElement($path,1,$pieceOfPath)->getFace();
            } catch (\Face\Exception\RootFaceReachedException $e){
                $pieceOfPath[0]="";
                $pieceOfPath[1]=$path;
                $parentFace=$this->baseFace;
            }

            $childElement=$parentFace->getElement($pieceOfPath[1]);


            $joinSql = FQuery::__doFQLJoinTable($path,$face,$parentFace,$childElement,$pieceOfPath[0],$this->dotToken);



            $sql.=$joinSql;
        }

        return $sql;
    }
    
    public function prepareWhereClause(){
        if(null===$this->where || empty($this->where))
            return "";
        
        
        $newString=$this->where;

        $matchArray = [];
        preg_match_all("#~([a-zA-Z0-9_]\\.{0,1})+#", $newString,$matchArray);
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