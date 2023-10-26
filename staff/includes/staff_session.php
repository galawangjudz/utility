<?php 
if (($_SESSION['user_type'] !== 'Staff') && ($_SESSION['user_type'] !== 'Cashier') && ($_SESSION['user_type'] !== 'Admin'))  {
   
    unset($_SESSION['alogin']);
	session_destroy(); // destroy session
    ?> 
    <script>
    window.location = "../index.php";
    </script>
    <?php
}

?>