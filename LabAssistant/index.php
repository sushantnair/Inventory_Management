<?php 
    session_start();
    //If a user is logged in and is a lab-assistant
    if (isset($_SESSION['logged']) && $_SESSION['role']=='lab-assistant') 
    {
        header('Location:dash_lab.php');
    }
    //If a user is logged in and is not a lab-assistant
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
    //If a user is not logged in
    else
    {
        header('Location:../logout.php');
    }
?>