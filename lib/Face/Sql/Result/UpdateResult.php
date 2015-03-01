<?php


namespace Face\Sql\Result;


class UpdateResult {

    /**
     * @var \PDOStatement
     */
    protected $statement;

    function __construct(\PDOStatement $statement)
    {
        $this->statement = $statement;
    }

    /**
     * @return \PDOStatement
     */
    public function getPdoStatement()
    {
        return $this->statement;
    }

    /**
     * @return mixed
     */
    public function countAffectedRows()
    {
        return $this->statement->rowCount();
    }
}