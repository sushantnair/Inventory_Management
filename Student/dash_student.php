<?php 
    session_start();
    if (isset($_SESSION['logged']) && $_SESSION['role']=='student') 
    {
        $id=$_SESSION['id'];
    }
    else if (isset($_SESSION['logged']) && $_SESSION['role']!='student')
    {
        $role=$_SESSION['role'];
        if($role=='admin')
            header('Location:../Admin/dash_admin.php'); 
        else if($role=='lab-assistant')
            header('Location:../LabAssistant/dash_lab.php');    
        else
            header('Location:../logout.php');
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
    
    <a href='../logout.php'>SIGN OUT</a>
</body>
</html>