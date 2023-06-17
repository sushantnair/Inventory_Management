<?php 
    session_start();
    if (isset($_SESSION['logged']) && $_SESSION['role']=='admin') 
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
    <title>Document</title>
</head>
<body>
    
</body>
</html>