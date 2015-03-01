<?php

namespace Face\Sql\Result;


class InsertResult {

    /**
     * @var \PDOStatement
     */
    protected $pdoStatement;
    protected $insertId;

    function __construct(\PDOStatement $pdoStatement, $insertId)
    {
        $this->pdoStatement = $pdoStatement;
        $this->insertId = $insertId;
    }

    /**
     * @return \PDOStatement
     */
    public function getPdoStatement()
    {
        return $this->pdoStatement;
    }

    /**
     * @return mixed
     */
    public function getInsertId()
    {
        return $this->insertId;
    }

    /**
     * @return mixed
     */
    public function countAffectedRows()
    {
        return $this->pdoStatement->rowCount();
    }


}