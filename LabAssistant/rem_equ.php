<?php 
    session_start();
    if (isset($_SESSION['logged']) && $_SESSION['role']=='lab-assistant') 
    {
        $id=$_SESSION['id'];
    }
    else if (isset($_SESSION['logged']) && $_SESSION['role']!='lab-assistant')
    {
        include 'connection.php';
		$role=$_SESSION['role'];
		if($role=='admin')
			header('Location:../Admin/dash_admin.php');    
		else if($role=='student')
			header('Location:../Student/dash_student.php');    
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