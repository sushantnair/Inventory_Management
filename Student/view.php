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
            header('Location:../Admin/index.php'); 
        else if($role=='lab-assistant')
            header('Location:../LabAssistant/index.php');    
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
    <!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">-->
    <link rel="stylesheet" href="../CSS/bootstrap.min.css">
    <!-- using an offline copy saves time spent for loading bootstrap from online source  -->
    <!-- <link rel="stylesheet" href="CSS/styles.css"> -->
</head>
<body>
    
<div>
        
        <button onclick="window.location.href='index.php'"> 
            Dash
        </button>
        <button onclick="window.location.href='view.php'"> 
            View Labs
        </button>
        <button onclick="window.location.href='equ.php'"> 
            View Equipment and Requests
        </button>
        <button onclick="window.location.href='../logout.php'"> 
           Signout
        </button>
    </div>
    <div class="search-container">
        <form action="" method="post" style="text-align:center"> <!-- style aligns the two input elements to be centred relative to each other -->
            <input type="text" name="search" id="search" style="text-align:center;" placeholder="Enter lab which you want to search for">
            <br>
            <button class="btn btn-primary" type="submit" value="Search">Submit</button>
        </form>
    </div>
    <div class="row col-lg-12 card card-body table-responsive">
        <table class="table table-centered table-nowrap mb-0">
            <thead>
                <tr>
                    <!-- HEADINGS -->
                    <th scope="col">Lab Num<br></th>
                    <th scope="col">Lab Name</th>
                    <th scope="col">Department</th>
                    <th scope="col">Active</th>
                    <th scope="col">Lab Assistant</th>
                    <th scope="col">View<br></th>
                </tr>
            </thead>
            <?php
                    // $parts = parse_url(basename($_SERVER['REQUEST_URI']));
                if(isset($_POST['search'])) 
                {
                    $search = $_POST['search'];
                    $sql_table_display = "SELECT * 
                                            FROM labs
                                            WHERE (labname LIKE '%$search%' OR
                                                labno LIKE '%$search%' OR
                                                dept LIKE '%$search%' OR
                                                active LIKE '%$search%' OR
                                                assistname LIKE '%$search%' OR
                                                assistid LIKE '%$search%')";
                    $result_table_display = mysqli_query($conn,$sql_table_display);
                    if(!$result_table_display){
                        echo "There is some problem in fetching equipment data.";
                        return;
                    }
                } else {
                    $result_table_display = mysqli_query($conn,"SELECT * FROM labs");
                    if(!$result_table_display){
                        echo "There is some problem in fetching equipment data.";
                        return;
                    }
                }    

                    while($row = mysqli_fetch_array($result_table_display, MYSQLI_ASSOC)) 
                    {    
                        ?>
                        <tr>
                            <form action="lab.php" method="post">
                                <input type="text" value="<?php echo $row['labno']?>" style="display:none" name="labno" id="labno">
                                <td><?php echo $row['labno']?></td>
                                <td><?php echo $row['labname']?></td>
                                <td><?php echo $row['dept']?></td>
                                <td><?php echo $row['active'] ?></td>
                                <td> <?php echo $row['assistname'] ?> </td>
                                <td>
                                    
                                    <button class="button1" type="submit" name="lab">
                                        View Lab
                                    </button>
                                </td>
                            </form>
                        </tr>
                 <?php
                    }
                 ?>
            <tbody>

    </body>
</html>







