<?php 
    session_start();
    //If a user is logged in and is a User
    if (isset($_SESSION['logged']) && $_SESSION['role']=='user') 
    {
        include('../connection.php');
        $id=$_SESSION['id'];
    }
    //If a user is logged in and is not a User
    else if (isset($_SESSION['logged']) && $_SESSION['role']!='user')
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

    <title>IM-KJSCE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" /><!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">-->
    <!-- <link rel="stylesheet" href="../CSS/bootstrap.min.css"> -->
    <!-- using an offline copy saves time spent for loading bootstrap from online source  -->
    <link rel="stylesheet" href="./CSS/styles.css">
</head>
<body style="background-color: #f8f9fc;overflow-x: hidden;">
    <?php include('../Components/sidebar.php') ?>
    <div class="position-absolute row pe-4 top-0 mx-4" style="left: 100px; width: calc(100% - 100px);">
    <form action="" method="post" style="text-align:center;">
            <br>
            <div class="row">
                <div class="col-md-4">
                </div>
                <div class="col-md-1 pe-0 mt-1">
                    <label for="search">Search</label>
                </div>
                <div class="col-md-2 ps-0">
                    <input type="text" class="form-control" id="search" name="search">
                </div>
                <div class="col-md-1 pe-0">
                <input class="btn btn-outline-danger alert-danger" type="submit" value="Search"><br><br>
                </div>
            </div>
        </form>
    
    <div class="row col-lg-12 card card-body table-card table-responsive">
        <table class="mb-0">
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
                        echo "There is some problem in fetching equipment ddata.";
                        return;
                    }
                } else {
                    $result_table_display = mysqli_query($conn,"SELECT * FROM labs");
                    if(!$result_table_display){
                        echo "There is some problem in fetching equipment daata.";
                        return;
                    }
                }    

                    while($row = mysqli_fetch_array($result_table_display, MYSQLI_ASSOC)) 
                    {    
                        ?>
                        <tr>
                            <form action="lab.php" method="get">
                                <input type="text" value="<?php echo $row['labno']?>" style="display:none" name="labno" id="labno">
                                <td><?php echo $row['labno']?></td>
                                <td class="lname"><?php echo $row['labname']?></td>
                                <td class="lname"><?php echo $row['dept']?></td>
                                <td><?php echo $row['active'] ?></td>
                                <td class="lname"> <?php echo $row['assistname'] ?> </td>
                                <td>
                                    
                                    <button class="btn btn-outline-dark" type="submit">
                                        View Lab
                                    </button>
                                </td>
                            </form>
                        </tr>
                 <?php
                    }
                 ?>
        </table>
    </div>
    </div>
    

    </body>
</html>







