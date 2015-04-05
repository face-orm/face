<?php

namespace Face\Exception;

use Face\Exception;

class QueryFailedException extends  Exception
{

    protected $statement;

    public function __construct(\PDOStatement $statement)
    {
        parent::__construct($statement->errorInfo()[2]);
        $this->statement = $statement;
    }

    /**
     * @return \PDOStatement
     */
    public function getPDOStatement()
    {
        return $this->statement;
    }
}