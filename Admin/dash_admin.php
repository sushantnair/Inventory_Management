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
    <link rel="stylesheet" type="text/css" href="../CSS/bootstrap.min.css">
    <!-- ../ is used to go one level up from Admin folder. -->
    <style>
        html, body{
            height: 100%;
        }
        /* the html and body elements are set to have a height of 100% using height: 100%;. This ensures that the body element will take up the full height of the viewport */
        body{
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .dash_admin_box{
            width: auto;
            height: auto;
            background-color: azure;
            border: solid 2px black;
            padding: 10px;
        }
        a{
            font-size: 20px;
            color: black;
        }
        p,h6{
            text-align: center;
        }
    </style>
    <title>IM-KJSCE | Admin Dashboard</title>
</head>
<body>
    <div class="dash_admin_box">
        <h6><span style="float: left; text-decoration: underline;">User ID: <?php echo $id; ?></span><span style="float: right;">Role: Administrator</span></h6><br>
        <p>Please select an option suitable for the operation you want to undertake</p>
        <button class="btn btn-primary btn-block" onclick="window.location.href='manage_assist.php'"> 
            Manage Lab Assistants
        </button>
        <br>
        <button class="btn btn-primary btn-block" onclick="window.location.href='manage_lab.php'">
            Manage Labs
        </button>
        <br>
        <button class="btn btn-primary btn-block" onclick="window.location.href='../logout.php'">
            Signout
        </button>
        <!--changed from all caps for user convenience-->
    </div>
</body>
</html>
