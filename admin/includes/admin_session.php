<?php 
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true || $_SESSION['user_type'] !== 'Admin') {
   
   /*  unset($_SESSION['alogin']);
	session_destroy(); // destroy session */
    session_id($_SESSION['user_session_id']);
    session_destroy(); // destroy session
   /*  header("location:index.php");  */
        ?> 
    <script>
    window.location = "../index.php";
    </script>
    <?php
}




?>