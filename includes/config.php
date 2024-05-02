<?php
ini_set('date.timezone','Asia/Manila');
date_default_timezone_set('Asia/Manila');
define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PASS','');
define('DB_NAME','db_utility');
define('base_url','http://192.168.0.111/utility/');

#$conn = mysqli_connect('localhost','root','','db_utility') or die(mysqli_error());

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (mysqli_connect_errno()) {
    die("Database connection failed: " . mysqli_connect_error());
}

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


date_default_timezone_set('Asia/Manila');
function calculate_time_ago($date_created) {
    $now = time();
    $created_at = strtotime($date_created);
    $time_ago = $now - $created_at;
    if ($time_ago < 60) { // Less than 1 minute ago
        $time_unit = $time_ago == 1 ? 'sec' : 'secs';
        $due_label = '<strong class="label label-danger">' . $time_ago . ' ' . $time_unit . ' ago</strong>';
    } elseif ($time_ago < 3600) { // Less than 1 hour ago
        $time_ago = floor($time_ago / 60); // Convert to minutes
        $time_unit = $time_ago == 1 ? 'min' : 'mins';
        $due_label = '<strong class="label label-danger">' . $time_ago . ' ' . $time_unit . ' ago</strong>';
    } elseif ($time_ago < 86400) { // Less than 1 day ago
        $time_ago = floor($time_ago / 3600); // Convert to hours
        $time_unit = $time_ago == 1 ? 'hr' : 'hrs';
        $due_label = '<strong class="label label-danger">' . $time_ago . ' ' . $time_unit . ' ago</strong>';
    } elseif ($time_ago < 604800) { // Less than 1 week ago
        $time_ago = floor($time_ago / 86400); // Convert to days
        $time_unit = $time_ago == 1 ? 'day' : 'days';
        $due_label = '<strong class="label label-warning">' . $time_ago . ' ' . $time_unit . ' ago</strong>';
    } elseif ($time_ago < 2592000) { // Less than 1 month ago
        $time_ago = floor($time_ago / 604800); // Convert to weeks
        $time_unit = $time_ago == 1 ? 'week' : 'weeks';
        $due_label = '<strong class="label label-primary">' . $time_ago . ' ' . $time_unit . ' ago</strong>';
    } elseif ($time_ago < 31536000) { // Less than 1 year ago
        $time_ago = floor($time_ago / 2592000); // Convert to months
        $time_unit = $time_ago == 1 ? 'month' : 'months';
        $due_label = '<strong class="label label-success">' . $time_ago . ' ' . $time_unit . ' ago</strong>';
    } else {// More than 1 year ago
        $time_ago = ceil($time_ago / 31536000); // Convert to years
        $time_unit = $time_ago == 1 ? 'year' : 'years';
        $due_label = '<strong class="label label-default">' . $time_ago . ' ' . $time_unit . ' ago</strong>';
    }
    return $due_label;
}

?>


