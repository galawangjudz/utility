<?php require_once('../includes/config.php'); ?>
<?php

session_start(); 
//Check whether the session variable SESS_MEMBER_ID is present or not
/* if (!isset($_SESSION['alogin']) || (trim($_SESSION['alogin']) == '')) { ?> */
if (!isset($_SESSION['user_session_id'])) { ?>
<script>
window.location = "../index.php";
</script>
<?php
}


$sess_id=$_SESSION['user_session_id'];
$session_id=$_SESSION['alogin'];
$session_depart = $_SESSION['dept'];
$session_role = $_SESSION['user_type'];




?>