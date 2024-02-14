
<?php

// Connect to the database (replace with your credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_utility";

$conn_notif = new mysqli($servername, $username, $password, $dbname);



// Check connection
if ($conn_notif->connect_error) {
    die("Connection failed: " . $conn_notif->connect_error);
}
session_start();
// Retrieve notifications for a specific user
$user_id = $_SESSION['alogin'];

$sql = "SELECT * FROM notifications WHERE user_id = $user_id ORDER BY created_at DESC";
$result = $conn_notif->query($sql);

$notifications = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
}


// Display notifications

// Output notifications as JSON
header('Content-Type: application/json');
echo json_encode($notifications);

$conn_notif->close();
?>
