<?php 
if (($_SESSION['user_type'] !== 'Staff') && ($_SESSION['user_type'] !== 'Cashier') && ($_SESSION['user_type'] !== 'Admin'))  {
   
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