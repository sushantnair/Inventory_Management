<?php 
    session_start();
    //If a user is logged in and is a student
    if (isset($_SESSION['logged']) && $_SESSION['role']=='student') 
    {
        include('../connection.php');
        $id=$_SESSION['id'];
    }
    //If a user is logged in and is not a student
    else if (isset($_SESSION['logged']) && $_SESSION['role']!='student')
    {
        $role=$_SESSION['role'];
        if($role=='admin')
            header('Location:../Admin/dash.php'); 
        else if($role=='lab-assistant')
            header('Location:../LabAssistant/dash.php');    
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
    <title>IM-KJSCE</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="../CSS/bootstrap.min.css">
    <link rel="stylesheet" href="CSS/styles.css">
    <style>
    
        /* Desktop view */
        @media (min-width: 768px) {
            .sidebar {
                width: 20%;
                float: left;
                background-color: azure; /* Add the background color */

            }
            .content {
                width: 80%;
                float: right;
            }
        }

        /* Mobile view */
        @media (max-width: 767px) {
            .sidebar {
                display: none;
                background-color: azure; /* Add the background color */

            }
            .content {
                width: 100%;
            }
            
        }
        /* Logo styles */
        .logo {
            width: 250px; /* Adjust the size as needed */
            margin: 20px; /* Add margin to position the logo */
            position: absolute;
            top: 10px; /* Adjust the top value to position the logo vertically */
            left: 10px; /* Adjust the left value to position the logo horizontally */
        
        }

        
    </style>
</head>
<body>

    <div class="sidebar">
    <img src="SomaiyaLogo.jpg" alt="Logo" class="logo">

        <h6><u>User ID:</u> <?php echo $id; ?></h6>
        <h6><u>Role:</u> Student</h6>
        <br>
        <p>Please select an option suitable for the operation you want to undertake</p>
        <button class="btn btn-primary btn-block" onclick="window.location.href='view.php'">View Labs</button>
        <br><br>
        <button class="btn btn-primary btn-block" onclick="window.location.href='equ.php'">View Equipment and Requests</button>
        <br><br><br><br><br><br>
        <button class="btn btn-primary btn-block" onclick="window.location.href='../logout.php'">Signout</button>
    </div>

    <div class="content">
        <!-- Your content here -->
    </div>

    <!-- Scripts -->
    <!-- Include Bootstrap and other scripts here -->
    <script src="../JS/bootstrap.min.js"></script>

</body>
</html>