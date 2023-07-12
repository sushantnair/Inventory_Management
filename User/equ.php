<?php 
    session_start();
    //If a user is logged in and is a User
    if (isset($_SESSION['logged']) && $_SESSION['role']=='user') 
    {
        include('../connection.php');
        $id=$_SESSION['id'];
        if(isset($_POST['lab']))
        {
            $labno=$_POST['labno'];
            // echo $labno;
        }
        if(isset($_POST['return']))
        {
            $labno=$_POST['labno'];
            $dsrno=$_POST['dsrno'];
            $requan=$_POST['requan'];
            $update_lend_details=mysqli_query($conn,"UPDATE lend SET lendquan=lendquan-$requan WHERE (lendfrom='$labno' AND lendto='$id' AND dsrno='$dsrno')");
            if(!$update_lend_details)
            {
                echo mysqli_error($conn);
                die();
            }
            $lend_transaction=mysqli_query($conn,"UPDATE $labno SET toquan=toquan-$requan,quantity=quantity+$requan WHERE dsrno='$dsrno'");
            if(!$lend_transaction)    //error in executing query
            {
                echo "Error in updating new lending transaction in lab table<br>";
                echo mysqli_error($conn);
                die();
            }
            
            $fetch_lend_details=mysqli_query($conn,"SELECT lendquan FROM lend WHERE (lendfrom='$labno' AND lendto='$id' AND dsrno='$dsrno')");
            $fetch_lend_details_array=mysqli_fetch_array($fetch_lend_details);
            $remain_lend_quan=$fetch_lend_details_array['lendquan'];
            if($remain_lend_quan<=0)
            {
                $delete_lend_transaction=mysqli_query($conn,"DELETE FROM lend WHERE lendfrom='$labno' AND lendto='$id' AND dsrno='$dsrno'");
            }
        }
        if(isset($_POST['delete']))
        {
            $labno=$_POST['labno'];
            $dsrno=$_POST['dsrno'];
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" /><!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">-->
    <!-- <link rel="stylesheet" href="../CSS/bootstrap.min.css"> -->
    <!-- using an offline copy saves time spent for loading bootstrap from online source  -->
    <link rel="stylesheet" href="./CSS/styles.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function(){
            $('button[data-bs-toggle="tab"]').on('show.bs.tab', function(e) {
                localStorage.setItem('activeTab', $(e.target).attr('data-bs-target'));
            });
            var activeTab = localStorage.getItem('activeTab');
            if(activeTab){
                $('#myTab button[data-bs-target="' + activeTab + '"]').tab('show');
            }
        });
    </script>

</head>
<body style="background-color: #f8f9fc;overflow-x: hidden;">
    <?php include('../Components/sidebar.php') ?>
    <div class="position-absolute row pe-4 top-0 mx-4" style="left: 100px; width: calc(100% - 100px);">

        <!-- Search bar -->
        <div class="search-container">
        <form action="" method="post" style="text-align:center;">
                <br>
                <div class="row">
                    <div class="col-md-2">
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
        </div>
            <!-- Nav tabs -->
        <div>
            <ul class="nav nav-tabs justify-content-center h4" id="myTab" role="tablist">
                <li class="nav-item mx-3" role="presentation">
                    <button class="nav-link" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Equipment Borrowed</button>
                </li>
                <li class="nav-item mx-3" role="presentation">
                    <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Equipment Requests</button>
                </li>
            </ul>

        </div>
        <div class="tab-content">
            <!-- Equipment Lent  -->
            <div class="tab-pane" id="home" role="tabpanel" aria-labelledby="home-tab">
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
                                <th scope="col">Lab</th>
                                <th scope="col">Return</th>
                                <!-- <th scope="col">View<br></th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if(isset($_POST['search'])) 
                                {
                                    $search = $_POST['search'];
                                    $sql_table_display = "SELECT * 
                                                            FROM lend
                                                            WHERE lendto='$id' AND
                                                                (dsrno LIKE '%$search%' OR
                                                                lendquan LIKE '%$search%' OR
                                                                lendfrom LIKE '%$search%')";
                                    $result_table_display = mysqli_query($conn,$sql_table_display);
                                    if(!$result_table_display){
                                        echo "There is some problem in fetching equipment data.";
                                        return;
                                    }
                                } else {
                                    $result_table_display = mysqli_query($conn,"SELECT * FROM lend WHERE lendto='$id'");
                                    if(!$result_table_display){
                                        echo "There is some problem in fetching equipment data.";
                                        return;
                                    }
                                }
                                while($row = mysqli_fetch_array($result_table_display, MYSQLI_ASSOC)) 
                                {    

                                    $dsrno=$row['dsrno']; 
                                    $labno=$row['lendfrom'];
                                    $equ_details=mysqli_query($conn,"SELECT * FROM $labno WHERE dsrno='$dsrno'");
                                    $eqrow=mysqli_fetch_array($equ_details,MYSQLI_ASSOC);
                                    ?>
                                    <tr>
                                            <td><?php echo $eqrow['eqname']?></td>
                                            <td><?php echo $eqrow['eqtype']?></td>
                                            <td><?php echo $row['dsrno']?></td>
                                            <td><?php echo $row['lendquan']?></td>
                                            <td><?php echo $eqrow['desc1'] ?></td>
                                            <td> <?php echo $eqrow['desc2'] ?> </td>
                                            <td> <?php echo $row['lendfrom'] ?> </td>                                
                                            <form action="equ.php" method="post">
                                            <input type="text" name="dsrno" value="<?php echo $row['dsrno']; ?>" style="display:none;">
                                            <input type="text" name="labno" value="<?php echo $labno; ?>" style="display:none;">
                                            <td>
                                                <button class="btn btn-outline-dark" type="button" name="request" style="width:85px;" data-bs-toggle="modal" data-bs-target="#staticBackdropreq<?php echo str_replace(array('/','(',')'), array('_','open','close'), strtolower($row['dsrno']));?>">
                                                    Return 
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
                                                                    
                                                                    $eqname=$eqrow['eqname'];
                                                                    $eqtype=$eqrow['eqtype'];
                                                                    $dsrno=$row['dsrno'];
                                                                    $quantity=$row['lendquan'];
                                                                    echo "Equipment Name: <strong>".$eqname."</strong><br>";
                                                                    echo "Equipment Type: <strong>".$eqtype."</strong><br>";
                                                                    echo "Equipment DSR No: <strong>".$dsrno."</strong><br>";
                                                                    echo "Lended Quantity: <strong>".$quantity."</strong><br>";
                                                                    echo "Returning To: <strong>".$labno."</strong><br><hr>";
                                                                    
                                                                ?>
                                                                <div class="form-floating col-12">

                                                                    <input type="number" class="form-control" name="requan" id="requan" min ="1" max="<?php echo $row['quantity'];?>" required>                             
                                                                    <label class="label ms-2" for="requan">Returning Quantity</label>        
                                                                    <hr>
                                                                    <p style="font-size: small; margin:0;">Click 'Return' to raise return request of the equipment</p>
                                                                    <p style="font-size: small; margin:0;">Click 'Cancel' to dismiss the popup for now.</p>
                                                                </div>
                                                            </div>

                                                            <div class="modal-footer">
                                                                <button type="button" class="btn alert-danger" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" name="return" class="btn btn-danger">Return</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            
                                        </form>
                                    </tr>
                                    <?php
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
                
            
            <div class="tab-pane" id="profile" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
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
                                <th scope="col">Lab</th>
                                <th scope="col">Delete</th>
                                <!-- <th scope="col">View<br></th> -->
                            </tr>
                        </thead>
                        <?php    
                            if(isset($_POST['search'])) 
                            {
                                $search = $_POST['search'];
                                $sql_table_display = "SELECT * 
                                                        FROM request
                                                        WHERE id='$id' AND
                                                            (dsrno LIKE '%$search%' OR
                                                            id LIKE '%$search%' OR
                                                            requan LIKE '%$search%')";
                                $result_table_display = mysqli_query($conn,$sql_table_display);
                                if(!$result_table_display){
                                    echo "There is some problem in fetching equipment data.";
                                    return;
                                }
                            } else {
                                $result_table_display = mysqli_query($conn,"SELECT * FROM request WHERE id='$id'");
                                if(!$result_table_display){
                                    echo "There is some problem in fetching equipment data.";
                                    return;
                                }
                            }                               
                            while($row = mysqli_fetch_array($result_table_display, MYSQLI_ASSOC)) 
                            {    

                                $dsrno=$row['dsrno']; 
                                $labno=$row['labno'];
                                $equ_details=mysqli_query($conn,"SELECT * FROM $labno WHERE dsrno='$dsrno'");
                                $eqrow=mysqli_fetch_array($equ_details,MYSQLI_ASSOC);
                                ?>
                                <tr>
                                        <td><?php echo $eqrow['eqname']?></td>
                                        <td><?php echo $eqrow['eqtype']?></td>
                                        <td><?php echo $row['dsrno']?></td>
                                        <td><?php echo $row['requan']?></td>
                                        <td><?php echo $eqrow['desc1'] ?></td>
                                        <td> <?php echo $eqrow['desc2'] ?> </td>
                                        <td> <?php echo $row['labno'] ?> </td>
                                        
                                        <form action="equ.php" method="post">
                                        <input type="text" name="dsrno" value="<?php echo $row['dsrno']; ?>" style="display:none;">
                                        <input type="text" name="labno" value="<?php echo $labno; ?>" style="display:none;">
                                        <td>
                                            <input class="btn btn-outline-dark" type="submit" name="delete" value="Delete"> 
                                                
                                        </td>  
                                    </form>
                                </tr>
                                <?php
                            }
                        ?>
                    </table>
                </div>
            </div>
            
    </div>
    
    </body>
</html>