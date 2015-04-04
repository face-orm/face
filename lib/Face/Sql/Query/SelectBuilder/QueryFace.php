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
    protected $limit;
    protected $offset;
    protected $columns = [];

    protected $basePath;


    function __construct($basePath, EntityFace $face)
    {
        $this->face = $face;
        $this->basePath = $basePath;
    }

    /**
     * @return mixed
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param mixed $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @return mixed
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param mixed $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
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

        if(null === $columns){
            $this->columns = [];
        }else if(!is_array($columns)){
            throw new BadParameterException("First parameter of Queryface should be an array. " . gettype($columns) . " given");
        }else{
            $this->columns = $columns;
        }

        return $this;
    }

    /**
     * @param $column
     * @return $this
     */
    public function addColumn($column, $alias = null){
        $this->columns[] = ["column" => $column, "alias" => $alias];
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
    public function getColumnsReal($userPrefix = false){

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
                        $column = $column["column"];
                    }

                    if ($column{0} == "!") {
                        $realPath = $this->_makePath(substr($column, 1));
                        unset($finalColumns[$realPath]);
                    } else {
                        $elm = $this->getFace()->getDirectElement($column);
                        $realPath = $this->_makePath($column);
                        if(!$alias){
                            $alias = $realPath;
                        }
                        $finalColumns[$realPath] = new Column($realPath, $alias, $elm);
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
                $realPath = $this->_makePath($elm->getName());
                if(!isset($columns[$realPath])) {
                    $columns[$realPath] = new Column($realPath, $realPath, $elm);
                }
            }
        }
    }

    protected function _includeAllColumns(&$columns){

        foreach($this->face->getElements() as $elm){
            if($elm->isValue()){
                $realPath = $this->_makePath($elm->getName());
                $columns[$realPath] = new Column($realPath, $realPath, $elm);
            }
        }

    }

    protected function _makePath($columnName){
        return $this->basePath . "." . $columnName; // TODO  : dot token
    }


}