<?php
    session_start();
    include('connection.php');
    //Delete session variables
    unset($_SESSION["id"]);
    unset($_SESSION["role"]);
    if(isset($_SESSION['labno'])){
        unset($_SESSION['labno']);
    }
    session_destroy();
    mysqli_close($conn);
    header("Location: index.php");
    $conn->close();
?>