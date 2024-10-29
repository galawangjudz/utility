<?php 
require_once('../includes/config.php');

if (session_status() == PHP_SESSION_NONE) {
    session_name('session_utility');
    session_start();
   
}
if (isset($_SESSION['user_session_id'], $_SESSION['alogin'], $_SESSION['dept'], $_SESSION['user_type'])) {
    $sess_id = $_SESSION['user_session_id'];
    $session_id = $_SESSION['alogin'];
    $session_depart = $_SESSION['dept'];
    $session_role = $_SESSION['user_type'];

    $query = " SELECT user_session_id FROM tblemployees where emp_id ='".$_SESSION['alogin']."'";

    $result = $conn->query($query);

    foreach ($result as $row)
    {
        if($_SESSION['user_session_id'] != $row['user_session_id'])
        {
            $data['output'] = 'logout';

        }else{
            $data['output'] = 'login';

        }

        
    }
    
    
}
echo json_encode($data);
?>