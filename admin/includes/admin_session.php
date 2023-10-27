<?php 
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true || $_SESSION['user_type'] !== 'Admin') {
   
    unset($_SESSION['alogin']);
	session_destroy(); // destroy session
    ?> 
    <script>
    window.location = "../index.php";
    </script>
    <?php
}

echo $_SESSION['alogin'];
?>