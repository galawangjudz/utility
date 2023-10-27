<?php
ini_set('date.timezone','Asia/Manila');
date_default_timezone_set('Asia/Manila');
define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PASS','');
define('DB_NAME','db_utility');
define('base_url','http://localhost/utility/');

$conn = mysqli_connect('localhost','root','','db_utility') or die(mysqli_error());

// Establish database connection.
try
{
$dbh = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME,DB_USER, DB_PASS,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
}
catch (PDOException $e)
{
exit("Error: " . $e->getMessage());
}


$dsn = "pgadmin4"; // Replace with your DSN name
$user = "glicelo";    // Replace with your database username
$pass = "admin12345";    // Replace with your database password

$conn2 = odbc_connect($dsn, $user, $pass);


// Check if the connection was successful
if (!$conn2) {
    die("Connection failed: " . odbc_errormsg());
}

?>


