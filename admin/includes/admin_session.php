<?php 
if (!isset($_SESSION['user_session_id'])) { ?>
    <script>
    window.location = "../index.php";
    </script>
    <?php
    }

if ($_SESSION['user_type'] !== 'Admin') {

    if (isset($_SESSION['user_session_id'])) {
        session_id($_SESSION['user_session_id']);
        session_destroy(); // destroy session
    }
        ?> 
    <script>
    window.location = "../index.php";
    </script>

    <?php
   
}




?>