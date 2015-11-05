<?php

namespace Face\Sql\Query\SelectBuilder;

use Face\Core\EntityFace;
use Face\Exception\BadParameterException;
use Face\Sql\Query\Clause\Select\Column;

class QueryFace {

    /**
     * @var EntityFace
     */
    protected $face;
    protected $columns = [];

    protected $basePath;

    protected $isSilent = false;


    function __construct($basePath, EntityFace $face)
    {
        $this->face = $face;
        $this->basePath = $basePath;
    }

    /**
     * @param bool $silent
     */
    public function setSilent($silent){
        $this->isSilent = $silent;
    }

    public function isSilent(){
        return $this->isSilent;
    }


    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param array $columns
     * @return $this
     */
    public function setColumns($columns)
    {
        $this->columns = [];

        if(null === $columns){
            return $this;
        }else if(!is_array($columns)){
            throw new BadParameterException("First parameter of Queryface should be an array. " . gettype($columns) . " given");
        }else{


            foreach($columns as $k=>$v){

                if($v{0} == '!' || $v == "*"){
                    $this->addColumn($v);
                }else{

                    if(is_int($k)){
                        $alias = null;
                        $column = $v;
                    }else{
                        $alias = $v;
                        $column = $k;
                    }

                    $this->addColumn($column, $alias);
                }

            }

        }

        return $this;
    }

    /**
     * @param $column
     * @return $this
     */
    public function addColumn($column, $alias = null){
        if(null === $alias){
            $this->columns[] = $column;
        }else{
            $this->columns[] = ["column" => $column, "alias" => $alias];
        }
        return $this;
    }

    /**
     * @return EntityFace
     */
    public function getFace()
    {
        return $this->face;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->basePath;
    }


    /**
     * @param bool $userPrefix
     * @return Column[] list of joined columns with this format :   $array["real.path"] = @see Face\Sql\Query\Clause\Select\Column;
     */
    public function getColumnsReal(){

        if($this->isSilent()){
            return [];
        }

        $finalColumns = [];


        if(count($this->columns) == 0){
            $this->_includeAllColumns($finalColumns);
        }else {
            foreach ($this->columns as $column) {
                if ($column == "*") {
                    $this->_includeAllColumns($finalColumns);
                } else {

                    if(is_array($column)){
                        $alias = $column["alias"];
                        $column = $column["column"];
                    }else{
                        $alias = null;
                    }

                    if ($column{0} == "!") {
                        $realPath = $this->makePath(substr($column, 1));
                        unset($finalColumns[$realPath]);
                    } else {
                        $elm = $this->getFace()->getDirectElement($column);
                        $realPath = $this->makePath($column);
                        if(!$alias){
                            $alias = $realPath;
                        }

                        $elementColumn = new Column\ElementColumn($this->getPath(), $elm);
                        $elementColumn->setQueryAlias($alias);
                        $finalColumns[$realPath] = $elementColumn;
                    }

                }
            }



        }

        $this->_includeIdentifiers($finalColumns);

        return $finalColumns;

    }

    protected function _includeIdentifiers(&$columns){
        foreach($this->face->getElements() as $elm){
            if($elm->isValue() && $elm->isIdentifier()){
                $realPath = $this->makePath($elm->getName());
                if(!isset($columns[$realPath])) {
                    $column = new Column\ElementColumn($this->getPath(), $elm);
                    $column->setQueryAlias($realPath);
                    $columns[$realPath] = $column;
                }
            }
        }
    }

    private function _includeAllColumns(&$columns){

        foreach($this->face->getElements() as $elm){
            if($elm->isValue()){
                $realPath = $this->makePath($elm->getName());
                $column = new Column\ElementColumn($this->getPath(), $elm);
                $column->setQueryAlias($realPath);
                $columns[$realPath] = $column;
            }
        }

    }

    public function makePath($columnName){
        return $this->basePath . "." . $columnName;
    }


}
