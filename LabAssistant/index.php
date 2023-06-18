<?php 
    session_start();
    if (isset($_SESSION['logged']) && $_SESSION['role']=='lab-assistant') 
    {
        header('Location:dash_lab.php');
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