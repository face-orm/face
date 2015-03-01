<?php
/**
 * Created by PhpStorm.
 * User: bob
 * Date: 3/1/15
 * Time: 12:17 PM
 */

namespace Face\Sql\Result;


class DeleteResult {

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