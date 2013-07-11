<?php


namespace Face\Sql\DB;

/**
 * Keys object contains login informations for the Database
 *
 * @author Soufiane Ghzal
 */
class Keys {
    
     protected $dbType;
     protected $dbname;
     protected $host;
     protected $port;
    
     protected $username;
     protected $passwd;
     
     protected $encoding;
     
     function __construct($dbType, $dbname, $host, $username, $passwd, $port=null, $encoding=null) {
         $this->dbType = $dbType;
         $this->dbname = $dbname;
         $this->host = $host;
         $this->username = $username;
         $this->passwd = $passwd;
         $this->port=$port;
         $this->encoding=$encoding;
     }

     
     public function getDbType() {
         return $this->dbType;
     }

     public function setDbType($dbType) {
         $this->dbType = $dbType;
     }

     public function getDbname() {
         return $this->dbname;
     }

     public function setDbname($dbname) {
         $this->dbname = $dbname;
     }

     public function getHost() {
         return $this->host;
     }

     public function setHost($host) {
         $this->host = $host;
     }

     public function getPort() {
         return $this->port;
     }

     public function setPort($port) {
         $this->port = $port;
     }

     public function getUsername() {
         return $this->username;
     }

     public function setUsername($username) {
         $this->username = $username;
     }

     public function getPasswd() {
         return $this->passwd;
     }

     public function setPasswd($passwd) {
         $this->passwd = $passwd;
     }

     public function getEncoding() {
         return $this->encoding;
     }

     public function setEncoding($encoding) {
         $this->encoding = $encoding;
     }


     
}

?>
