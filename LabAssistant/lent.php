<?php 
    session_start();
    //If a user is logged in and is a lab-assistant
    if (isset($_SESSION['logged']) && $_SESSION['role']=='lab-assistant') 
    {
        // CONNECT DATABASE
        include('../connection.php');

        // USER ID
        $id=$_SESSION['id'];
        $labno=$_SESSION['labno'];

        // IF RETURNING EQUIPMENT
        if(isset($_POST['return'])||isset($_POST['returnall']))
        {
            //FORM DATA
            $lendto=$_POST['labno'];
            $dsrno=$_POST['dsrno'];
            $requan=$_POST['requan'];

            //FIND LENDING LAB DETAILS
            $query=mysqli_query($conn,"SELECT * FROM lend WHERE lendto='$lendto' AND dsrno='$dsrno'");
            $row=mysqli_fetch_array($query,MYSQLI_ASSOC);
            $lendfrom=$row['lendfrom'];     //LAB EQUIPMENT LENT FROM
            $lendquan=$row['lendquan'];     //QUANTITY OF LENT EQUIPMENT
            if($requan==$lendquan || isset($_POST['returnall']))
            {
                //DELETE FROM LEND TABLE
                $remove_lend=mysqli_query($conn,"DELETE FROM lend WHERE lendto='$lendto' AND dsrno='$dsrno' AND lendfrom='$lendfrom'");
                
                //DELETE FROM TABLE OF RETURNING LAB
                $remove_lendfrom = mysqli_query($conn," DELETE FROM $lendto WHERE dsrno='$dsrno'");
                $requan=$lendquan;
            }
            else
            {
                $reduce_lend_table=mysqli_query($conn,"UPDATE lend SET lendquan=lendquan-$requan WHERE (lendto='$lendto' AND dsrno='$dsrno' AND lendfrom='$lendfrom')");
                $reduce_lend_this_lab = mysqli_query($conn,"UPDATE $lendto SET quantity=(quantity-$requan) ,byquan=(byquan-$requan) WHERE dsrno='$dsrno'");
            }
            
                
                
            // UPDATE VALUES IN ORIGINAL TABLE
            $remove_lendto = mysqli_query($conn,"  UPDATE $lendfrom SET toquan=(toquan-$requan), quantity=(quantity+$requan)WHERE dsrno='$dsrno'");
            header('Location:lent.php');
        }
        if(isset($_POST['lend']))   //LENDING TO User/PROFESSOR
        {
            //FETCH FORM DATA
            $lendfrom=$_POST['labno'];
            $dsrno=$_POST['dsrno'];
            $lendquan=$_POST['lendquan'];
            $lendto=$_POST['lendto'];
            
            $check_ownership=mysqli_query($conn,"SELECT * FROM lend WHERE dsrno='$dsrno' AND lendto='$lendfrom'");

            if(mysqli_num_rows($check_ownership)==1)    //OWNED BY OTHER LAB
            {
                $fetch_owner=mysqli_fetch_array($check_ownership,MYSQLI_ASSOC);
                $orignal_labno=$fetch_owner['lendfrom'];
                $orignal_lend_quan=$fetch_owner['lendquan'];


                if($orignal_lend_quan==$lendquan)   //ALL EQUIPMENTS BEING LEND FROM LAB-B TO User (OWNED BY LAB-A)
                {
                    $check_prev_lend=mysqli_query($conn,"SELECT * FROM lend WHERE lendto='$lendto' AND dsrno='$dsrno' AND lendfrom='$orignal_labno'");
                    if(mysqli_num_rows($check_prev_lend)==0)    //User NOT LENT SAME EQUIPMENT FROM LAB-A
                    {
                        //SHIFT 'lend' TRANSACTION 'lendto' FROM LAB-B TO User
                        $insert_transaction=mysqli_query($conn,"UPDATE lend SET lendto='$lendto' WHERE lendto='$lendfrom' AND dsrno='$dsrno' AND lendfrom='$orignal_labno'");
                    }
                    else    //User PREVIOUSLY LENT SAME EQUIPMENT FROM LAB-A
                    {
                        $insert_transaction=mysqli_query($conn,"UPDATE lend SET lendquan=(lendquan+$lendquan) WHERE lendto='$lendto' AND dsrno='$dsrno' AND lendfrom='$orignal_labno'");
                        $delete_old_transaction=mysqli_query($conn,"DELETE FROM lend WHERE lendto='$lendfrom' AND dsrno='$dsrno'");
                        
                    }
                    //DELETE FROM LAB-B
                    $remove_old_lend=mysqli_query($conn,"DELETE FROM $lendfrom WHERE dsrno='$dsrno'");
                }
                else
                {
                    $check_prev_lend=mysqli_query($conn,"SELECT * FROM lend WHERE lendto='$lendto' AND dsrno='$dsrno' AND lendfrom='$orignal_labno'");
                    if(mysqli_num_rows($check_prev_lend)==0)    //User NOT LENT SAME EQUIPMENT FROM LAB-A
                        $insert_transaction=mysqli_query($conn,"INSERT INTO lend(lendfrom,dsrno,lendquan,lendto) values('$orignal_labno','$dsrno',$lendquan,'$lendto')");
                                       
                    else    //User PREVIOUSLY LENT SAME EQUIPMENT FROM LAB-A
                        $insert_transaction=mysqli_query($conn,"UPDATE lend SET lendquan=(lendquan+$lendquan) WHERE lendto='$lendto' AND dsrno='$dsrno' AND lendfrom='$orignal_labno'");
                    
                    //MODIFY OLD LENDING BETWEEN LAB-A AND LAB-B
                    $modify_old=mysqli_query($conn,"UPDATE lend SET lendquan=(lendquan-$lendquan) WHERE lendto='$lendfrom' AND dsrno='$dsrno' AND lendfrom='$orignal_labno'");
                    //SUBTRACT FROM LAB-B
                    $update_old_lend=mysqli_query($conn,"UPDATE $lendfrom SET quantity=quantity-$lendquan, byquan=byquan-$lendquan WHERE dsrno='$dsrno'");

                }
                $delete_request=mysqli_query($conn,"DELETE FROM request WHERE (labno='$lendfrom' AND dsrno='$dsrno' AND id='$lendto') ");
                
            }
            else    //OWNED BY THIS LAB
            {
                
                //CHECKING PREVIOUSLY LENDING
                $check_prev_lend=mysqli_query($conn,"SELECT * FROM lend WHERE lendfrom='$lendfrom' AND dsrno='$dsrno' AND lendto='$lendto'");
                
                if(mysqli_num_rows($check_prev_lend)==1)    //PREVIOUSLY LENT SAME EQUIPMENT TO THIS USER
                    $update_previous=mysqli_query($conn,"UPDATE lend SET lendquan=lendquan+$lendquan WHERE lendfrom='$lendfrom' AND dsrno='$dsrno' AND lendto='$lendto'");                    
                else    //NOT LENT SAME EQUIPMENT TO THIS USER
                    $insert_transaction=mysqli_query($conn,"INSERT INTO lend(lendfrom,dsrno,lendto,lendquan) VALUES('$lendfrom','$dsrno',$lendto,$lendquan) ");
                
                //UPDATE CURRENT LAB TABLE
                $lend_transaction=mysqli_query($conn,"UPDATE $lendfrom SET toquan=toquan+$lendquan,quantity=quantity-$lendquan WHERE dsrno='$dsrno'");
                //DELETE FROM 'request' TABLE
                $delete_request=mysqli_query($conn,"DELETE FROM request WHERE (labno='$lendfrom' AND dsrno='$dsrno' AND id='$lendto') ");
            }                            
            header('Location:lent.php');
        }
        if(isset($_POST['deny']))
        {
            $lendfrom=$_POST['labno'];
            $dsrno=$_POST['dsrno'];
            $lendto=$_POST['lendto'];
            $deny_request=mysqli_query($conn,"DELETE FROM request WHERE labno='$lendfrom' AND dsrno='$dsrno' AND id='$lendto'");
        }
        
    }
    //If a user is logged in and is not a lab-assistant
    else if (isset($_SESSION['logged']) && $_SESSION['role']!='lab-assistant')
    {
		$role=$_SESSION['role'];
		if($role=='admin')
			header('Location:../Admin/index.php');    
		else if($role=='user')
			header('Location:../User/index.php');    
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
<html lang="en" class="notranslate" translate="no">
<head>
    <meta charset="UTF-8">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
    <meta name="google" content="notranslate" /> 
    <title>IM-KJSCE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="CSS/styles.css">
    <!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">-->
    <!-- <link rel="stylesheet" href="../CSS/bootstrap.min.css"> -->
    <!-- using an offline copy saves time spent for loading bootstrap from online source  -->
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
<body style="background-color: #f8f9fc; overflow-x: hidden;">
    
    <?php include('../Components/sidebar.php') ?>
    <div class="position-absolute row pe-4 top-0 mx-4" style="left: 100px; width: calc(100% - 100px);">

    <!-- Search bar -->
        <form action="" method="post" style="text-align:center;">
            <br>
            <div class="row">
                <div class="col-md-2">
                </div>
                <div class="col-md-1 pe-0 mt-1">
                    <label for="search">Search</label>
                </div>
                <div class="col-md-2 ps-0">
                    <input type="text" class="form-control" name="search" id="search">
                </div>
                <div class="col-md-1 pe-0">
                    <input class="btn btn-outline-danger alert-danger" type="submit" value="Search"><br><br>
                </div>
            </div>
        </form>
        
    

        <!-- MAIN TABLE  -->
        <?php

            $result_lab_fetch = mysqli_query($conn,"SELECT * FROM labs WHERE labno = '$labno'");
            if(!$result_lab_fetch){
                echo "Lab details could not be fetched.";
                return;
            }
            $row = mysqli_fetch_array($result_lab_fetch, MYSQLI_ASSOC);
        ?>
        <!-- Nav tabs -->
        <div>
            <ul class="nav nav-tabs justify-content-center h4" id="myTab" role="tablist">
                <li class="nav-item mx-3" role="presentation">
                    <button class="nav-link" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Equipment Lent</button>
                </li>
                <li class="nav-item mx-3" role="presentation">
                    <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Equipment Borrowed</button>
                </li>
                <li class="nav-item mx-3" role="presentation">
                    <button class="nav-link" id="messages-tab" data-bs-toggle="tab" data-bs-target="#messages" type="button" role="tab" aria-controls="messages" aria-selected="false">Equipment Requests</button>
                </li>
                
            </ul>

        </div>
    

        <!-- Tab panes -->
        <div class="tab-content">
            <!-- Equipment Lent  -->
            <div class="tab-pane" id="home" role="tabpanel" aria-labelledby="home-tab">
            <div class="row col-lg-12 card card-body table-card table-responsive">
                    <table class="mb-0">
                        <thead>
                            <tr>
                                <!-- HEADINGS -->
                                <th scope="col">Equipment Name<br></th>
                                <th scope="col">DSR Number<br></th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Description 1</th>
                                <th scope="col">Description 2</th>
                                <th scope="col">Lent To</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                            <?php 
                                //FETCH LENDING DATA FOR THIS LAB
                                if(isset($_POST['search'])) 
                                {
                                    $search = $_POST['search'];
                                    $sql_given_equipment_fetch = "SELECT *
                                                                    FROM lend NATURAL JOIN $labno
                                                                    WHERE lendfrom = '$labno' AND
                                                                        (lend.dsrno LIKE '%$search%' OR 
                                                                        lend.lendto LIKE '%$search%' OR
                                                                        lend.lendquan LIKE '%$search%' OR
                                                                        $labno.eqname LIKE '%$search%' OR
                                                                        $labno.eqtype LIKE '%$search%' OR
                                                                        $labno.desc1 LIKE '%$search%' OR
                                                                        $labno.desc2 LIKE '%$search%' OR
                                                                        $labno.cost LIKE '%$search%')";
                                    $result_given_equipment_fetch = mysqli_query($conn, $sql_given_equipment_fetch);
                                    if(!$result_given_equipment_fetch){
                                        echo "There is some problem in fetching lab equipment data.";
                                        return;
                                    }
                                } else {
                                    $sql_given_equipment_fetch = "SELECT *
                                                                FROM lend NATURAL JOIN $labno
                                                                WHERE lendfrom = '$labno'";
                                    $result_given_equipment_fetch = mysqli_query($conn, $sql_given_equipment_fetch);
                                    if(!$result_given_equipment_fetch){
                                        echo "There is some problem in fetching lab equipment data.";
                                        return;
                                    }
                                }
                                
                                while($row = mysqli_fetch_array($result_given_equipment_fetch, MYSQLI_ASSOC))
                                {
                                    // $dsrno=$row['dsrno'];
                                    // $equ_details=mysqli_query($conn,"SELECT * FROM $labno WHERE dsrno='$dsrno'");
                                    // $eqrow=mysqli_fetch_array($equ_details,MYSQLI_ASSOC);
                                    ?>
                                        
                                        <tr>
                                        <td><?php echo $row['eqname'];?></td>
                                        <td><?php echo $row['dsrno'];?></td>
                                        <td><?php echo $row['lendquan'];?></td>
                                        <td><?php echo $row['desc1'];?></td>
                                        <td><?php echo $row['desc2'];?></td>
                                        <td><?php echo $row['lendto'];?></td>

                                        </tr>
                                    <?php
                                    }
                                
                            ?>
                            
                        </tbody>
                    </table>
                </div>
                

            </div>

            <!-- Equipment Borrowed  -->
            <div class="tab-pane" id="profile" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
            <div class="row col-lg-12 card card-body table-card table-responsive">
                    <table class="mb-0">
                        <thead>
                            <tr>
                                <!-- HEADINGS -->
                                <th scope="col">Equipment Name<br></th>
                                <th scope="col">DSR Number<br></th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Description 1</th>
                                <th scope="col">Description 2</th>
                                <th scope="col">Lent From</th>
                                <th scope="col">Update<br></th>
                            </tr>
                        </thead>
                        
                        <tbody>
                            <?php
                                if(isset($_POST['search']))
                                {
                                    $search = $_POST['search'];
                                    
                                    $sql_borrowed_equipment_fetch = "SELECT *
                                                                    FROM lend NATURAL JOIN $labno
                                                                    WHERE lendto = '$labno' AND
                                                                        (lend.dsrno LIKE '%$search%' OR
                                                                        lend.lendquan LIKE '%$search%' OR
                                                                        $labno.eqname LIKE '%$search%' OR
                                                                            $labno.eqtype LIKE '%$search%' OR
                                                                            $labno.desc1 LIKE '%$search%' OR
                                                                            $labno.desc2 LIKE '%$search%' OR
                                                                            $labno.cost LIKE '%$search%')";
                                    $result_borrowed_equipment_fetch = mysqli_query($conn, $sql_borrowed_equipment_fetch);
                                    if(!$result_borrowed_equipment_fetch){
                                        echo "There is some problem in fetching lab equipment data.";
                                        return;
                                    }
                                } else {
                                    $sql_borrowed_equipment_fetch = "SELECT *
                                                                FROM lend
                                                                WHERE lendto = '$labno'";
                                    $result_borrowed_equipment_fetch = mysqli_query($conn, $sql_borrowed_equipment_fetch);
                                    if(!$result_borrowed_equipment_fetch){
                                        echo "There is some problem in fetching lab equipment data.";
                                        return;
                                    }
                                }
                                while($row = mysqli_fetch_array($result_borrowed_equipment_fetch, MYSQLI_ASSOC))
                                {
                                                            
                                    $lendfrom=$row['lendfrom'];
                                    $dsrno=$row['dsrno'];
                                    $labno=$row['lendto'];
                                    $equ_details=mysqli_query($conn,"SELECT * FROM $lendfrom WHERE dsrno='$dsrno'");
                                    $eqrow=mysqli_fetch_array($equ_details,MYSQLI_ASSOC);

                                    ?>
                                        
                                        <tr>
                                        <td><?php echo $eqrow['eqname'];?></td>
                                        <td><?php echo $eqrow['dsrno'];?></td>
                                        <td><?php echo $row['lendquan'];?></td>
                                        <td><?php echo $eqrow['desc1'];?></td>
                                        <td><?php echo $eqrow['desc2'];?></td>
                                        <td><?php echo $row['lendfrom'];?></td>
                                        <td><button name="return" style="width: 80px;" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#staticBackdropreturn<?php echo str_replace(array('/','(',')'), array('_','open','close'), strtolower($eqrow['dsrno']));?>">
                                            Return
                                        </button></td>
                                            <div class="modal fade" id="staticBackdropreturn<?php echo str_replace(array('/','(',')'), array('_','open','close'), strtolower($eqrow['dsrno']));?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title text-danger" id="staticBackdropLabel">Return Equipment</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body" style="text-align: center;">
                                                                <?php
                                                                    $dsrno=$eqrow['dsrno'];
                                                                    $fetch_equipment=mysqli_query($conn,"SELECT * FROM $labno WHERE dsrno='$dsrno'");
                                                                    $fetch_lab=mysqli_query($conn,"SELECT * FROM lend WHERE lendto='$labno' AND dsrno='$dsrno'");
                                                                    
                                                                    $labno_row=mysqli_fetch_array($fetch_lab,MYSQLI_ASSOC);
                                                                    $lendfrom=$labno_row['lendfrom'];

                                                                    $eqroww=mysqli_fetch_array($fetch_equipment,MYSQLI_ASSOC);
                                                                    $eqtype=$eqroww['eqtype'];
                                                                    $eqname=$eqroww['eqname'];
                                                                    $quantity=$eqroww['quantity'];
                                                                    echo "Equipment Name: <strong>".$eqname."</strong><br>";
                                                                    echo "Equipment Type: <strong>".$eqtype."</strong><br>";
                                                                    echo "Equipment DSR No: <strong>".$dsrno."</strong><br>";
                                                                    echo "Equipment Quantity: <strong>".$quantity."</strong><br>";
                                                                    echo "Returning to: <strong>".$lendfrom."</strong><br><hr>";
                                                                    
                                                                ?>
                                                                <form action="" method="post">  
                                                                    <input type="text" name="labno" value="<?php echo $labno; ?>" style="display:none;">
                                                                    <input type="text" name="dsrno" value="<?php echo $row['dsrno']; ?>" style="display:none;">              
                                                                    <div class="form-floating col-12">
                                                                        <input class="form-control" type="number" name="requan" id="requan" min="1" max="<?php echo $eqroww['quantity'];?>" required>
                                                                        <label class="label ms-2" for="requan">Returning Quantity</label>        
                                                                    </div>
                                                                    <hr>
                                                                    <p style="font-size: small; margin:0;">Click 'Return All' to return all quantity of the equipment</p>
                                                                    <p style="font-size: small;">Input quantity and click 'Return' to return some quantity of the equipment</p>
                                                            </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn alert-danger" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" name="return" class="btn btn-danger">Return</button>
                                                                </form>

                                                                <form action="" method="post" style="margin: 0;padding:0%;">  
                                                                    <input type="text" name="labno" value="<?php echo $labno; ?>" style="display:none;">
                                                                    <input type="text" name="dsrno" value="<?php echo $row['dsrno']; ?>" style="display:none;">    
                                                                    <button type="submit" name="returnall" class="btn btn-danger">Return All</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                            </div>
                                        
                                        </tr>
                                    <?php
                                    }
                            ?>
                            
                        </tbody>
                    </table>
            </div>

            </div>

            <!-- Equipment Requests  -->
            <div class="tab-pane" id="messages" role="tabpanel" aria-labelledby="messages-tab" tabindex="0">
            <div class="row col-lg-12 card card-body table-card table-responsive">
                        <table class="mb-0">
                            <thead>
                                <tr>
                                    <!-- HEADINGS -->
                                    <th scope="col">Equipment Name<br></th>
                                    <th scope="col">DSR Number<br></th>
                                    <th scope="col">Request Quantity</th>
                                    <th scope="col">Available Quantity</th>
                                    <th scope="col">Description 1</th>
                                    <th scope="col">Description 2</th>
                                    <th scope="col">Request From</th>
                                    <th scope="col">Lend Quantity</th>
                                </tr>
                            </thead>
                            
                            <tbody>
                                <?php 
                                    //FETCH LENDING DATA FOR THIS LAB
                                    if(isset($_POST['search']) && $_POST['search']!='')
                                    {
                                        $search = $_POST['search'];
                                        $sql_requested_equipment_fetch = "SELECT *
                                                                        FROM request JOIN $labno USING (dsrno)
                                                                        WHERE request.labno='$labno'
                                                                        AND (
                                                                        request.id LIKE '%$search%' OR
                                                                        request.dsrno LIKE '%$search%' OR
                                                                        request.requan LIKE '%$search%' OR                                                                        
                                                                        $labno.eqname LIKE '%$search%' OR
                                                                        $labno.desc1 LIKE '%$search%' OR
                                                                        $labno.desc2 LIKE '%$search%' OR
                                                                        $labno.quantity LIKE '%$search%'
                                                                        )";
                                        $result_requested_equipment_fetch = mysqli_query($conn, $sql_requested_equipment_fetch);
                                        if(!$result_requested_equipment_fetch){
                                            echo mysqli_error($conn);
                                            return;
                                        }
                                    } else {
                                        $sql_requested_equipment_fetch = "SELECT *
                                                                    FROM request
                                                                    WHERE labno = '$labno'";
                                        $result_requested_equipment_fetch = mysqli_query($conn, $sql_requested_equipment_fetch);
                                        if(!$result_requested_equipment_fetch){
                                            echo "There is some problem in fetching lab equipment data.";
                                            return;
                                        }
                                    }
                                    
                                    while($row = mysqli_fetch_array($result_requested_equipment_fetch, MYSQLI_ASSOC))
                                    {
                                        $dsrno=$row['dsrno'];
                                        $equ_details=mysqli_query($conn,"SELECT * FROM $labno WHERE dsrno='$dsrno'");
                                        $eqrow=mysqli_fetch_array($equ_details,MYSQLI_ASSOC);
                                        ?>
                                            
                                            <tr>
                                            <td><?php echo $eqrow['eqname'];?></td>
                                            <td><?php echo $eqrow['dsrno'];?></td>
                                            <td><?php echo $row['requan'];?></td>
                                            <td><?php echo $eqrow['quantity'];?></td>
                                            <td><?php echo $eqrow['desc1'];?></td>
                                            <td><?php echo $eqrow['desc2'];?></td>
                                            <td><?php echo $row['id'];?></td>
                                            <td style="width: 450px;">
                                            <button name="return" style="width: 100px;" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#staticBackdroprespond<?php echo str_replace(array('/','(',')'), array('_','open','close'), strtolower($eqrow['dsrno']));?>">
                                            Respond
                                        </button></td>
                                            <div class="modal fade" id="staticBackdroprespond<?php echo str_replace(array('/','(',')'), array('_','open','close'), strtolower($eqrow['dsrno']));?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title text-danger" id="staticBackdropLabel">Equipment Request</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body" style="text-align: center;">
                                                                <?php
                                                                    $dsrno=$eqrow['dsrno'];
                                                                    // echo $dsrno;
                                                                    $fetch_equipment=mysqli_query($conn,"SELECT * FROM $labno WHERE dsrno='$dsrno'");
                                                                    $fetch_user_id=mysqli_query($conn,"SELECT * FROM request WHERE labno='$labno'");
                                                                    
                                                                    $user_row=mysqli_fetch_array($fetch_user_id,MYSQLI_ASSOC);
                                                                    $user_id=$user_row['id'];
                                                                    $re_quan=$user_row['requan'];

                                                                    $fetch_user=mysqli_query($conn,"SELECT * FROM user WHERE id='$user_id'");
                                                                    $user_details=mysqli_fetch_array($fetch_user,MYSQLI_ASSOC);
                                                                    $user_name=$user_details['name'];
                                                                    $user_email=$user_details['email'];
                                                                    $user_dept=$user_details['dept'];

                                                                    $eqroww=mysqli_fetch_array($fetch_equipment,MYSQLI_ASSOC);
                                                                    $eqtype=$eqroww['eqtype'];
                                                                    $eqname=$eqroww['eqname'];
                                                                    $quantity=$eqroww['quantity'];
                                                                    echo "Equipment Name: <strong>".$eqname."</strong><br>";
                                                                    echo "Equipment DSR: <strong>".$dsrno."</strong><br>";
                                                                    echo "Equipment Type: <strong>".$eqtype."</strong><br>";
                                                                    echo "Equipment Quantity: <strong>".$quantity."</strong><br>";
                                                                    echo "Request Quantity: <strong>".$re_quan."</strong><hr>";
                                                                    echo "<u>Requesting User</u>: <br>";
                                                                    echo "Name: <strong>".$user_name."</strong><br>";
                                                                    echo "ID: <strong>".$user_id."</strong><br>";
                                                                    echo "Email: <strong>".$user_email."</strong><br>";
                                                                    echo "Dept: <strong>".$user_dept."</strong><br><hr>";
                                                                    
                                                                ?>
                                                                <form action="" method="post">  
                                                                    <input type="text" name="dsrno" value="<?php echo $row['dsrno']; ?>" style="display:none;">
                                                                    <input type="text" name="labno" value="<?php echo $labno; ?>" style="display:none;">
                                                                    <input type="text" name="lendto" value="<?php echo $row['id']; ?>" style="display:none;">
                                                                    <div class="form-floating col-12 mb-2">
                                                                        <input class="form-control" type="number" name="lendquan" id="lendquan" min ="1" max="<?php if($row['requan']>$eqrow['quantity']) echo $eqrow['quantity']; else echo $row['quantity'];?>" placeholder="Quantity" required>                                                                    
                                                                        <label class="label ms-2" for="lendquan">Lending Quantity</label>        
                                                                    </div>
                                                                    <hr>
                                                                    <p style="font-size: small; margin:0;">Click 'Lend' to lend the specified quantity of the equipment.</p>
                                                                    <p style="font-size: small; margin:0;">Click 'Deny  Request' to deny the request.</p>
                                                                    <p style="font-size: small;">Click 'Cancel' to dismiss the popup for now.</p>
                                                            </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn alert-danger" data-bs-dismiss="modal">Cancel</button>
                                                                <input class="btn btn-outline-dark" type="submit" name="lend" value="Lend Equipment"> 
                                                                </form>

                                                                <form action="" method="post" style="margin: 0;padding:0%;">  
                                                                    <input type="text" name="dsrno" value="<?php echo $row['dsrno']; ?>" style="display:none;">
                                                                    <input type="text" name="labno" value="<?php echo $labno; ?>" style="display:none;">
                                                                    <input type="text" name="lendto" value="<?php echo $row['id']; ?>" style="display:none;">
                                                                    <button class="btn btn-outline-danger" type="submit" name="deny"> 
                                                                        Deny Request
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                            </div>
                                            </td>
                                            </tr>
                                        <?php
                                        }
                                    
                                ?>
                                
                            </tbody>
                        </table>
                    </div>
            
            </div>
        </div>
        
    </div>

</body>
</html>