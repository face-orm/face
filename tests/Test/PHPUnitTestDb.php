<?php

namespace Test;

abstract class PHPUnitTestDb extends \PHPUnit_Extensions_Database_TestCase
{
    // instancie pdo seulement une fois pour le nettoyage du test/le chargement de la fixture
    static private $pdo = null;

    // instancie PHPUnit_Extensions_Database_DB_IDatabaseConnection seulement une fois par test
    private $conn = null;

    final public function getConnection()
    {
        if ($this->conn === null) {
            if (self::$pdo == null) {
                self::$pdo = new \PDO( $GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD'] );
            }
            $this->conn = $this->createDefaultDBConnection(self::$pdo, $GLOBALS['DB_DBNAME']);
        }

        return $this->conn;
    }


    protected function getDataSet()
    {
        return $this->createMySQLXMLDataSet('dataset.xml');
    }

    protected function setUp() {
        $conn=$this->getConnection();
        $conn->getConnection()->query("set foreign_key_checks=0");
        parent::setUp();
        $conn->getConnection()->query("set foreign_key_checks=1");
    }
}