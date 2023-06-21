<?php
session_start();
//If a user is logged in and is an admin
if (isset($_SESSION['logged']) && $_SESSION['role']=='admin') 
{
    include '../connection.php';
    $labassistant=$_POST['assistant'];
    if(isset($_POST['assist']) && $labassistant!='none')
    {   
        $labno=$_POST['labno'];     
        mysqli_query($conn,"UPDATE labs SET assistname='$labassistant' WHERE labno='$labno'");
    }    
    else if(isset($_POST['lab']))
    {    
        $labno=$_POST['labno'];
        mysqli_query($conn,"DELETE FROM labs WHERE labno='$labno'");
    }
    else if(isset($_POST['addlab']) && $_POST['dept']!='None')
    {
        $labno=$_POST['labno']; 
        $labname=$_POST['labname']; 
        $dept=$_POST['dept']; 
        $active=$_POST['active']; 
        $labassistant=$_POST['assistant'];
        mysqli_query($conn,"INSERT INTO labs(labname,dept,labno,active,assistname) values('$labname','$dept','$labno','$active','$labassistant')");

    }    
header('Location:manage_lab.php');
}
//If a user is logged in and is not an admin
else if (isset($_SESSION['logged']) && $_SESSION['role']!='admin')
{
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