<?php 
    session_start();
    //If a user is logged in and is a student
    if (isset($_SESSION['logged']) && $_SESSION['role']=='student') 
    {
        include('../connection.php');
        $id=$_SESSION['id'];
        if(isset($_POST['lab']))
        {
            $_SESSION['labno']=$_POST['labno'];
            $labno = $_SESSION['labno'];
            //The lab number is stored in a Session variable 'labno'
            // echo $labno;
        }
        if(isset($_POST['request']))
        {

            $dsrno=$_POST['dsrno']; 
            $labno=$_POST['labno'];  //REQUEST FROM LAB
            $quantity=$_POST['requan'];   //REQUESTING QUANTITY
            $fetch_equipment=mysqli_query($conn,"SELECT * FROM request WHERE labno='$labno' AND id=$id AND dsrno='$dsrno'");
            if(!$fetch_equipment)
            {
                echo mysqli_error($conn);
                die();
            }
            else 
            {
                if(mysqli_num_rows($fetch_equipment)==0)
                {
                    $insert_request=mysqli_query($conn,"INSERT INTO request(labno,id,dsrno,quantity) values('$labno',$id,'$dsrno',$quantity)");
                }
                
            }
        }
        if(isset($_POST['delrequest']))
        {

            $dsrno=$_POST['dsrno']; 
            $labno=$_POST['labno'];  //REQUEST FROM LAB
            $delete_request=mysqli_query($conn,"DELETE FROM request WHERE labno='$labno' AND id=$id AND dsrno='$dsrno'");
            if(!$delete_request)
            {
                echo mysqli_error($conn);
                die();
            }
        }

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" /><!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">-->
    <!-- <link rel="stylesheet" href="../CSS/bootstrap.min.css"> -->
    <!-- using an offline copy saves time spent for loading bootstrap from online source  -->
    <link rel="stylesheet" href="./CSS/styles.css">
</head>
<body style="background-color: #f8f9fc;overflow-x: hidden;">
    <?php include('../Components/sidebar.php') ?>
    <div class="position-absolute container row w-100 top-0 ms-4" style="left: 100px; z-index:100;">

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
    <div class="row col-lg-12 card card-body table-responsive">
        <table class="table table-centered table-nowrap mb-0">
            <thead>
                <tr>
                    <!-- HEADINGS -->
                    <th scope="col">Name<br></th>
                    <th scope="col">Type<br></th>
                    <th scope="col">DSR No</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Desc1</th>
                    <th scope="col">Desc2</th>
                    <th scope="col">Cost</th>
                    <th scope="col">Request Quantity</th>
                    <th scope="col">Request</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    // $parts = parse_url(basename($_SERVER['REQUEST_URI']));
                    
                    /*$sql_table_display = "SELECT * 
                                        FROM $labno";
                    $result_table_display = mysqli_query($conn, $sql_table_display);
                    if(!$result_table_display){
                        echo "There is some problem in the connection.";
                        return;
                    }
                    */
                    if(isset($_POST['search']))  
                    {
                        $search = $_POST['search'];
                        $labno = $_SESSION['labno'];
                        $sql_table_display = "SELECT * 
                                                FROM $labno
                                                WHERE (eqname LIKE '%$search%' OR
                                                    dsrno LIKE '%$search%' OR
                                                    eqtype LIKE '%$search%' OR
                                                    quantity LIKE '%$search%' OR
                                                    desc1 LIKE '%$search%' OR
                                                    desc2 LIKE '%$search%' OR
                                                    cost LIKE '%$search%')
                                              ";
                        $result_table_display = mysqli_query($conn,$sql_table_display);
                        if(!$result_table_display){
                            echo "There is some problem in fetching equipment data.";
                            return;
                        }
                    } else {
                        $result_table_display = mysqli_query($conn,"SELECT * FROM $labno");
                        if(!$result_table_display){
                            echo "There is some problem in fetching equipment data.";
                            return;
                        }
                    } 
                    while($row = mysqli_fetch_array($result_table_display, MYSQLI_ASSOC)) 
                    {    
                        $labno = $_SESSION['labno'];
                        $dsrno=$row['dsrno'];
                        ?>
                        <tr>
                            <form action="lab.php" method="post">
                                <input type="text" name="dsrno" value="<?php echo $row['dsrno']; ?>" style="display:none;">
                                <input type="text" name="labno" value="<?php echo $labno; ?>" style="display:none;">
                                <td><?php echo $row['eqname']?></td>
                                <td><?php echo $row['eqtype']?></td>
                                <td><?php echo $row['dsrno']?></td>
                                <td><?php echo $row['quantity']?></td>
                                <td><?php echo $row['desc1'] ?></td>
                                <td> <?php echo $row['desc2'] ?> </td>
                                <td> <?php echo $row['cost'] ?> </td>
                                <?php
                                    $fetch_requested=mysqli_query($conn,"SELECT quantity FROM request WHERE dsrno='$dsrno' AND labno='$labno' AND id=$id");
                                    $quan=mysqli_fetch_array($fetch_requested,MYSQLI_ASSOC);
                                    if(mysqli_num_rows($fetch_requested)==1)
                                    {
                                        ?>
                                        <td><?php echo $quan['quantity'];?></td>
                                        <td>
                                            <button class="btn btn-outline-dark" name="delrequest" style="width:85px;">
                                                Delete
                                            </button>
                                        </td>
                                        <?php 
                                    }
                                    else if(mysqli_num_rows($fetch_requested)==0)
                                    {
                                        ?>
                                        <td><input type="number" class="form-control" name="requan" id="requan" min ="1" max="<?php echo $row['quantity'];?>" style="width:150px; margin-left:5px;" required></td>                                
                                        <td>
                                            <button class="btn btn-outline-dark" name="request" style="width:85px;">
                                                Request
                                            </button>
                                        </td>
                                        <?php 
                                    }
                                    ?>
                                        
                                
                            </form>
                        </tr>

                        <?php
                    }
                    ?>
            </tbody>
        </table>
    </div>
    </div>
    
</body>
</html>







