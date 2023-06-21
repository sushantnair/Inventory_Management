<?php 
    session_start();
    //If a user is logged in and is a lab-assistant
    if (isset($_SESSION['logged']) && $_SESSION['role']=='lab-assistant') 
    {
        $id=$_SESSION['id'];
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../CSS/bootstrap.min.css">
    <title>IM-KJSCE</title>
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
        .dash_lab_box{
            width: auto;
            height: auto;
            background-color: azure;
            border: solid 2px black;
            padding: 10px;
            margin: 5vw;
        }
        a{
            font-size: 20px;
            color: black;
        }
        p, h6{
            text-align: center;
            padding: 5px;
        }
    </style>
</head>
<body>
    <div class="dash_lab_box">
        <h6><span style="float: left; text-decoration: underline;">User ID: <?php echo $id; ?></span><span style="float: right;">Role: Lab Assistant</span></h6><br>
        <p>Please select an option suitable for the operation you want to undertake</p>
        <button class="btn btn-primary btn-block" onclick="window.location.href='add_equ.php'"> 
            Add equipment
        </button>
        <br>
        <button class="btn btn-primary btn-block" onclick="window.location.href='rem_equ.php'"> 
            Remove equipment
        </button>
        <br>
        <button class="btn btn-primary btn-block" onclick="window.location.href='lend_equ.php'"> 
            Lend equipment
        </button>
        <br>
        <button class="btn btn-primary btn-block" onclick="window.location.href='../logout.php'"> 
           Signout
        </button>
    </div>
    <!-- <a href='add_equ.php'>Add equipment</a>
    <br>
    <a href='rem_equ.php'>Remove equipment</a>
    <br>
    <a href='lend_equ.php'>Lend equipment</a>
    <br>
    <a href='../logout.php'>SIGN OUT</a> -->
</body>
</html>