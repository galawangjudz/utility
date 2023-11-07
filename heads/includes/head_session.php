<?php 
if ($_SESSION['user_type'] !== 'Head') {
   
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