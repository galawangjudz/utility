<?php 
require_once('../includes/config.php');

if (isset($_SESSION['alogin'])) {


if (isset($_POST['op']) && isset($_POST['np'])
    && isset($_POST['c_np'])) {

	function validate($data){
       $data = trim($data);
	   $data = stripslashes($data);
	   $data = htmlspecialchars($data);
	   return $data;
	}

	$op = validate($_POST['op']);
	$np = validate($_POST['np']);
	$c_np = validate($_POST['c_np']);
    
    if(empty($op)){
	  echo "<script>location.replace(_base_url_+'admin/?page=change_password&erorr=Old Password is required');</script>";
      //header("Location: change-password.php?error=Old Password is required");
	  exit();
    }else if(empty($np)){
	  echo "<script>location.replace(_base_url_+'admin/?page=change_password&error=New Password is required');</script>";
      //header("Location: change-password.php?error=New Password is required");
	  exit();
    }else if($np !== $c_np){
	  echo "<script>location.replace(_base_url_+'admin/?page=change_password&error=The confirmation password  does not match');</script>";
      //header("Location: change-password.php?error=The confirmation password  does not match");
	  exit();
    }else {
    	// hashing the password
    	$op = md5($op);
    	$np = md5($np);
        $id = $_SESSION['alogin'];

        $sql= "SELECT * FROM tblemployees where emp_id ='$id' AND Password ='$op'";
        $result = mysqli_query($conn, $sql);
        if(mysqli_num_rows($result) === 1){
        	
        	$sql_2 = "UPDATE tblemployees
        	          SET password='$np'
        	          WHERE emp_id='$id'";
        	mysqli_query($conn, $sql_2);
			echo "<script>location.replace(_base_url_+'admin/?page=change_password&success=Your password has been changed successfully');</script>";
        	//header("Location: change-password.php?success=Your password has been changed successfully");
	        exit();

        }else {
			echo "<script>location.replace(_base_url_+'admin/?page=change_password&error=Incorrect password');</script>";
        	
        	//header("Location: change-password.php?error=Incorrect password");
	        exit();
        }

    }

    
}else{
	header("Location: change-password.php");
	exit();
}

}else{
     header("Location: index.php");
     exit();
}