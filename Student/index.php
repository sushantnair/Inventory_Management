<?php 
    session_start();
    if (isset($_SESSION['logged']) && $_SESSION['role']=='student') 
    {
        header('Location:dash_student.php');
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