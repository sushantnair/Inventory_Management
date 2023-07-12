<?php 
    session_start();
    //If a user is logged in and is a User
    if (isset($_SESSION['logged']) && $_SESSION['role']=='user') 
    {
        include('../connection.php');
        $id=$_SESSION['id'];
        if(isset($_GET['labno']))
        {
            $labno=$_GET['labno'];
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
                    $insert_request=mysqli_query($conn,"INSERT INTO request(labno,id,dsrno,requan) values('$labno',$id,'$dsrno',$quantity)");
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
    <link rel="stylesheet" href="../css/bootstrap.css">
    <script src="../js/bootstrap.bundle.js"></script>
<!-- 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script> -->
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
                    <th scope="col">Name<br></th>
                    <th scope="col">Type<br></th>
                    <th scope="col">DSR No</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Desc1</th>
                    <th scope="col">Desc2</th>
                    <th scope="col">Cost</th>
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
                                    $fetch_requested=mysqli_query($conn,"SELECT requan FROM request WHERE dsrno='$dsrno' AND labno='$labno' AND id=$id");
                                    $quan=mysqli_fetch_array($fetch_requested,MYSQLI_ASSOC);
                                    if(mysqli_num_rows($fetch_requested)==1)
                                    {
                                        ?>
                                        <td><?php echo $quan['requan'];?></td>
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
                                        <td>
                                            <button class="btn btn-outline-dark" type="button" name="request" style="width:85px;" data-bs-toggle="modal" data-bs-target="#staticBackdropreq<?php echo str_replace(array('/','(',')'), array('_','open','close'), strtolower($row['dsrno']));?>">
                                                Request
                                            </button>
                                            
                                            <div class="modal fade" id="staticBackdropreq<?php echo str_replace(array('/','(',')'), array('_','open','close'), strtolower($row['dsrno']));?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title text-danger" id="staticBackdropLabel">Request Equipment</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <?php
                                                                // $dsrno=$row['dsrno'];
                                                                // $fetch_equipment=mysqli_query($conn,"SELECT * FROM $labno WHERE dsrno='$dsrno'");
                                                                // $fetch_lab=mysqli_query($conn,"SELECT * FROM lend WHERE lendto='$labno' AND dsrno='$dsrno'");
                                                                // if(!$fetch_equipment)
                                                                // {
                                                                //     echo mysqli_error($conn);
                                                                //     die();
                                                                // }
                                                                // $labno_row=mysqli_fetch_array($fetch_lab,MYSQLI_ASSOC);
                                                                // $lendfrom=$labno_row['lendfrom'];

                                                                // $eqrow=mysqli_fetch_array($fetch_equipment,MYSQLI_ASSOC);
                                                                $eqname=$row['eqname'];
                                                                $eqtype=$row['eqtype'];
                                                                $dsrno=$row['dsrno'];
                                                                $quantity=$row['quantity'];
                                                                echo "Equipment Name: <strong>".$eqname."</strong><br>";
                                                                echo "Equipment Type: <strong>".$eqtype."</strong><br>";
                                                                echo "Equipment DSR No: <strong>".$dsrno."</strong><br>";
                                                                echo "Equipment Quantity: <strong>".$quantity."</strong><br>";
                                                                echo "Requesting From: <strong>".$labno."</strong><br><hr>";
                                                                
                                                            ?>
                                                            <div class="form-floating col-12">

                                                                <input type="number" class="form-control" name="requan" id="requan" min ="1" max="<?php echo $row['quantity'];?>" required>                             
                                                                <label class="label ms-2" for="requan">Requesting Quantity</label>        
                                                                <hr>
                                                                <p style="font-size: small; margin:0;">Click 'Request' to request given quantity of the equipment</p>
                                                                <p style="font-size: small;" margin:0;"">Click 'Cancel' to dismiss the popup for now.</p>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn alert-danger" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" name="request" class="btn btn-danger">Request</button>

                                                                
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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







