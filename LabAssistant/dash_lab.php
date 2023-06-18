<?php 
    session_start();
    if (isset($_SESSION['logged']) && $_SESSION['role']=='lab-assistant') 
    {
        $id=$_SESSION['id'];
    }
    else
    {
        header('Location:../logout.php');
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IM-KJSCE</title>
</head>
<body>
    <a href='add_equ.php'>Add equipment</a>
    <br>
    <a href='rem_equ.php'>Remove equipment</a>
    <br>
    <a href='lend_equ.php'>Lend equipment</a>
    <br>
    <a href='../logout.php'>SIGN OUT</a>
</body>
</html>