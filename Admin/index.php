<?php 
    session_start();
    if (isset($_SESSION['logged']) && $_SESSION['role']=='admin') 
    {
        header('Location:dash_admin.php');
    }
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
    else
    {
        header('Location:../logout.php');
    }
?>