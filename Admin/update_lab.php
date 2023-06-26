<?php
session_start();
//If a user is logged in and is an admin
if (isset($_SESSION['logged']) && $_SESSION['role']=='admin') 
{
    include '../connection.php';
    $assistid=$_POST['assistant'];
    if(isset($_POST['assist']))
    //UPDATE ASSISTANTS
    {  
        $labno=$_POST['labno'];  
        if($assistid!=0) 
        {
            $query1=mysqli_query($conn,"SELECT * from user where id=$assistid");
            $row = mysqli_fetch_array($query1,MYSQLI_ASSOC);
            $name=$row['name'];
            mysqli_query($conn,"UPDATE labs SET assistid=$assistid, assistname='$name' WHERE labno='$labno'");
        }
        else 
        {
            mysqli_query($conn,"UPDATE labs SET assistid=0, assistname='' WHERE labno='$labno'");
        }
        
    }  
    if(isset($_POST['lab']))
    {    
        // DELETE LAB
        $labno=$_POST['labno']; // LAB-NUMBER
        mysqli_query($conn,"DELETE FROM labs WHERE labno='$labno'"); // DELETE LAB FROM LABS TABLE
        mysqli_query($conn,"DROP TABLE $labno");    //DROP LAB TABLE 
    }
    if(isset($_POST['addlab']) && $_POST['dept']!='None')   // CREATE LAB
    {
        //FORM DATA
        $labno=$_POST['labno']; 
        $labname=$_POST['labname']; 
        $dept=$_POST['dept']; 
        $active=$_POST['active']; 
        //INSERT LAB DETAILS IN LABS TABLE
        $insert_lab=mysqli_query($conn,"INSERT INTO labs (labname,dept,labno,active,assistname,assistid) values('$labname','$dept','$labno','$active','',0)");
        if(!$insert_lab)
            {
                echo mysqli_error($conn);
                die();
            }
            else
            {
                echo "Query succesfully executed!";
            } 
        //CREATE NEW TABLE FOR LAB USING LAB-NUMBER
        mysqli_query($conn,"CREATE TABLE $labno (eqname VARCHAR(250), dsrno VARCHAR(250), eqtype VARCHAR(250), quantity INT(4), desc1 VARCHAR(250), desc2 VARCHAR(250), cost FLOAT(10))");
    }    
header('Location:manage_lab.php');
}
//If a user is logged in and is not an admin
else if (isset($_SESSION['logged']) && $_SESSION['role']!='admin')
{
    $role=$_SESSION['role'];
    if($role=='lab-assistant')
        header('Location:../LabAssistant/dash.php');    
    else if($role=='student')
        header('Location:../Student/dash.php');    
    else
        header('Location:../logout.php');
}
//If a user is not logged in
else
{
    header('Location:../logout.php');
}
?>