<?php
    session_start();
    include('connection.php');
    //Delete session variables
    unset($_SESSION["id"]);
    unset($_SESSION["role"]);
    session_destroy();
    header("Location: index.php");
    mysqli_close($conn);
?>