<?php
    session_start();
    //Delete session variables
    unset($_SESSION["id"]);
    unset($_SESSION["role"]);
    session_destroy();
    header("Location: index.php");
?>