<?php 
    session_start();
    //If a user is logged in and is a student
    if (isset($_SESSION['logged']) && $_SESSION['role']=='student') 
    {
        header('Location:dash_student.php');
    }
    //If a user is logged in and is not a student
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
    //If a user is not logged in redirect
    else
    {
        header('Location:../logout.php');
    }
?>