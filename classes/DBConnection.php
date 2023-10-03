<?php
if(!defined('DB_SERVER')) define('DB_SERVER',"localhost");
if(!defined('DB_USERNAME')) define('DB_USERNAME',"root");
if(!defined('DB_PASSWORD')) define('DB_PASSWORD',"");
if(!defined('DB_NAME')) define('DB_NAME',"db_utility");
if(!defined('DB_SERVER')){
    require_once("../includes/config.php");
}
class DBConnection{

    private $host = DB_SERVER;
    private $username = DB_USERNAME;
    private $password = DB_PASSWORD;
    private $database = DB_NAME;
    
    public $conn;
    
    public function __construct(){

        if (!isset($this->conn)) {
            
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
            
            if (!$this->conn) {
                echo 'Cannot connect to database server';
                exit;
            }            
        }    
        
    }
    public function __destruct(){
        $this->conn->close();
    }
}

class PSQLConnection{

    private $host = DB_SERVER;
    private $username = DB_USERNAME;
    private $password = DB_PASSWORD;
    private $database = DB_NAME;
    
    public $conn;
    
    public function __construct(){

        $dsn = "pgadmin4"; // Replace with your DSN name
        $user = "glicelo";    // Replace with your database username
        $pass = "admin12345";    // Replace with your database password

        if (!isset($this->conn)) {
            
            $test_conn = odbc_connect($dsn, $user, $pass);
            #$test_conn = odbc_connect("Driver=pgadmin4;Server=192.168.60.102;Port=5432;Database=TEST2;Uid=glicelo;Pwd=admin12345;");
            if (!$test_conn) {
                die("ODBC Connection Failed: " . odbc_errormsg());
            }   
        }    
        
    }
    public function __destruct(){
        $this->conn->close();
    }
}
?>