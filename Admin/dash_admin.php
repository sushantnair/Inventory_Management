<?php 
    session_start();
    //If a user is logged in and is an admin
    if (isset($_SESSION['logged']) && $_SESSION['role']=='admin') 
    {
        $id=$_SESSION['id'];
    }
    //If a user is logged in and is not an admin
    else if (isset($_SESSION['logged']) && $_SESSION['role']!='admin')
    {
        include 'connection.php';
		$role=$_SESSION['role'];
		if($role=='lab-assistant')
			header('Location:../LabAssistant/dash_lab.php');    
		else if($role=='student')
			header('Location:../Student/dash_student.php');    
        else
            header('Location:../logout.php');
    }
    //If a user is not logged in
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
    <a href='add_assist.php'>ADD LAB ASSISTANTS</a>
    <br>
    <a href='add_lab.php'>ADD LABS</a>
    <br>
    <a href='../logout.php'>SIGN OUT</a>
</body>
</html>