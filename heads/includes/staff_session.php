<?php 
if ($_SESSION['user_type'] !== 'Staff') {
   
    unset($_SESSION['alogin']);
	session_destroy(); // destroy session
    ?> 
    <script>
    window.location = "../index.php";
    </script>
    <?php
}

?>