<?php

namespace Face\Sql\Reader;

/**
 *
 * @author bobito
 */
interface QueryReaderInterface {
    
    /**
     * 
     * @param \PDOStatement $pdoStmt pdo statement to be read
     * @return \Face\Sql\Result\ResultSet ResultSet instance containing the data hydrated
     */
    public function read(\PDOStatement $pdoStmt);
    
}
