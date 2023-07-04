<?php 
    session_start();
    //If a user is logged in and is a lab-assistant
    if (isset($_SESSION['logged']) && $_SESSION['role']=='lab-assistant') 
    {
        // CONNECT DATABASE
        include('../connection.php');

        // USER ID
        $id=$_SESSION['id'];

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
        if(isset($_POST['lend']))   //LENDING TO STUDENT/PROFESSOR
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


                if($orignal_lend_quan==$lendquan)   //ALL EQUIPMENTS BEING LEND FROM LAB-B TO Student (OWNED BY LAB-A)
                {
                    $check_prev_lend=mysqli_query($conn,"SELECT * FROM lend WHERE lendto='$lendto' AND dsrno='$dsrno' AND lendfrom='$orignal_labno'");
                    if(mysqli_num_rows($check_prev_lend)==0)    //STUDENT NOT LENT SAME EQUIPMENT FROM LAB-A
                    {
                        //SHIFT 'lend' TRANSACTION 'lendto' FROM LAB-B TO Student
                        $insert_transaction=mysqli_query($conn,"UPDATE lend SET lendto='$lendto' WHERE lendto='$lendfrom' AND dsrno='$dsrno' AND lendfrom='$orignal_labno'");
                    }
                    else    //STUDENT PREVIOUSLY LENT SAME EQUIPMENT FROM LAB-A
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
                    if(mysqli_num_rows($check_prev_lend)==0)    //STUDENT NOT LENT SAME EQUIPMENT FROM LAB-A
                        $insert_transaction=mysqli_query($conn,"INSERT INTO lend(lendfrom,dsrno,lendquan,lendto) values('$orignal_labno','$dsrno',$lendquan,'$lendto')");
                                       
                    else    //STUDENT PREVIOUSLY LENT SAME EQUIPMENT FROM LAB-A
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
		else if($role=='student')
			header('Location:../Student/index.php');    
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

    <link rel="stylesheet" href="CSS/styles.css">
    <!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">-->
    <!-- <link rel="stylesheet" href="../CSS/bootstrap.min.css"> -->
    <!-- using an offline copy saves time spent for loading bootstrap from online source  -->

</head>
<body>
    <!-- TEMPORARY DASHBOARD -->
    <div>
        <button onclick="window.location.href='index.php'"> 
            Dashboard
        </button>
        <button onclick="window.location.href='view.php'"> 
            View Equipment
        </button>
        <button onclick="window.location.href='lent.php'"> 
            Lent Equipment
        </button>
        <button onclick="window.location.href='../logout.php'"> 
            Sign Out
        </button>        
    </div>

    <!-- Search bar -->
    <div class="search-container">
        <form action="" method="post" style="text-align:center"> <!-- style aligns the two input elements to be centred relative to each other -->
            <input type="text" name="search" id="search" style="text-align:center;" placeholder="Enter equipment which you want to search for">
            <br>
            <select id="filter" name="filter" placeholder="Select Filter" required>
                <option value="0" selected>Select Filter</option>
                <option value="1">Search only from Lent equipments</option>
                <option value="2">Search only from Borrowed equipments</option>
                <option value="3">Search only from Requested equipments</option>
                <option value="4">Search from Lent and Borrowed equipments</option>
                <option value="5">Search from Lent and Requested equipments</option>
                <option value="6">Search from Borrowed and Requested equipments</option>
                <option value="7">Search from all equipments</option>
            </select>
            <br>
            <button class="btn btn-primary" type="submit" value="Search">Submit</button>
        </form>
    </div>

    <!-- MAIN TABLE  -->
    <?php

    $result_lab_fetch = mysqli_query($conn,"SELECT * FROM labs WHERE assistid = $id");
    if(!$result_lab_fetch){
        echo "Lab details could not be fetched.";
        return;
    }
    $row = mysqli_fetch_array($result_lab_fetch, MYSQLI_ASSOC);
    $labno = $row['labno'];
    $filter = $_POST['filter'] ?? '';
    //  '   ??''    ' is added so that warning message is not shown before user selects a filter value.

    //{
        //TO DO: Improve the efficiency of Search button so that equipments can be searched by name instead of only DSR NO
        //and other attributes in the "lend" or "request" table.
    ?>
    <?php
        if($filter == '' || $filter == '1' || $filter == '4' || $filter == '5' || $filter == '7'){
            ?>
            <h4 style="text-align: center;">EQUIPMENTS LENT</h4>
            <div class="row col-lg-12 card card-body table-responsive">
                <table class="table table-centered table-nowrap mb-0">
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
                                                                        $labno.cost LIKE '%$search%');";
                                $result_given_equipment_fetch = mysqli_query($conn, $sql_given_equipment_fetch);
                                if(!$result_given_equipment_fetch){
                                    echo "There is some problem in fetching lab equipment data.";
                                    return;
                                }
                            } else {
                                $sql_given_equipment_fetch = "SELECT *
                                                            FROM lend
                                                            WHERE lendfrom = '$labno'";
                                $result_given_equipment_fetch = mysqli_query($conn, $sql_given_equipment_fetch);
                                if(!$result_given_equipment_fetch){
                                    echo "There is some problem in fetching lab equipment data.";
                                    return;
                                }
                            }
                            
                            while($row = mysqli_fetch_array($result_given_equipment_fetch, MYSQLI_ASSOC))
                            {
                                $dsrno=$row['dsrno'];
                                $equ_details=mysqli_query($conn,"SELECT * FROM $labno WHERE dsrno='$dsrno'");
                                $eqrow=mysqli_fetch_array($equ_details,MYSQLI_ASSOC);
                                ?>
                                    
                                    <tr>
                                    <td><?php echo $eqrow['eqname'];?></td>
                                    <td><?php echo $eqrow['dsrno'];?></td>
                                    <td><?php echo $row['lendquan'];?></td>
                                    <td><?php echo $eqrow['desc1'];?></td>
                                    <td><?php echo $eqrow['desc2'];?></td>
                                    <td><?php echo $row['lendto'];?></td>

                                    </tr>
                                <?php
                                }
                            
                        ?>
                        
                    </tbody>
                </table>
            </div>
            <?php
        }
         if ($filter == '' || $filter == '2' || $filter == '4' || $filter == '6' || $filter == '7') {
        ?>
            <h4 style="text-align: center;">EQUIPMENTS BORROWED</h4>
            <div class="row col-lg-12 card card-body table-responsive">
                <table class="table table-centered table-nowrap mb-0">
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
                                                                    lend.lendto LIKE '%$search%' OR
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
                                    <td><button name="return" style="width: 80px;" class="btn btn-outline-danger alert-danger" data-bs-toggle="modal" data-bs-target="#staticBackdropreturn<?php echo str_replace('/', '_', strtolower($eqrow['dsrno']));?>">
                                        Return
                                    </button></td>
                                        <div class="modal fade" id="staticBackdropreturn<?php echo str_replace('/', '_', strtolower($eqrow['dsrno']));?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="staticBackdropLabel">Returning</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <?php
                                                                $dsrno=$eqrow['dsrno'];
                                                                echo $dsrno;
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
                                                                echo "Equipment Type: <strong>".$dsrno."</strong><br>";
                                                                echo "Equipment Quantity: <strong>".$quantity."</strong><br>";
                                                                echo "Returning to: <strong>".$lendfrom."</strong><br><br>";
                                                                
                                                            ?>
                                                            <form action="" method="post">  
                                                                <input type="text" name="labno" value="<?php echo $labno; ?>" style="display:none;">
                                                                <input type="text" name="dsrno" value="<?php echo $row['dsrno']; ?>" style="display:none;">              
                                                                <div class="form-floating col-12">
                                                                    <input class="form-control" type="number" name="requan" id="requan" min ="1" max="<?php echo $row['quantity'];?>" required>
                                                                    <label class="label ms-2" for="lendquan">Returning Quantity</label>        
                                                                </div>
                                                                <p style="font-size: x-small; margin:0;">Click 'Return All' to return all quantity of the equipment</p>
                                                                <p style="font-size: x-small;">Input quantity and click 'Return' to return some quantity of the equipment</p>
                                                        </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn alert-danger" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" name="return" class="btn btn-danger">Return</button>
                                                            </form>

                                                            <form action="" method="post">  
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
        <?php
        }
             if ($filter == '' || $filter == '3' || $filter == '5' || $filter == '6' || $filter == '7' ) {
            //$request=mysqli_query($conn,"SELECT * FROM request WHERE labno='$labno'");
            ?>
                <h4 style="text-align: center;">REQUESTS</h4>
                <div class="row col-lg-12 card card-body table-responsive">
                    <table class="table table-centered table-nowrap mb-0">
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
                                if(isset($_POST['search']))
                                {
                                    $search = $_POST['search'];
                                    
                                    $sql_requested_equipment_fetch = "SELECT *
                                                                    FROM request, $labno
                                                                    WHERE labno = '$labno' AND
                                                                        (request.dsrno LIKE '%$search%' OR 
                                                                        request.id LIKE '%$search%' OR
                                                                        request.quantity LIKE '%$search%' OR
                                                                        $labno.eqname LIKE '%$search%' OR
                                                                        $labno.eqtype LIKE '%$search%' OR
                                                                        $labno.desc1 LIKE '%$search%' OR
                                                                        $labno.desc2 LIKE '%$search%' OR
                                                                        $labno.cost LIKE '%$search%')
                                                                    LIMIT 1";
                                    $result_requested_equipment_fetch = mysqli_query($conn, $sql_requested_equipment_fetch);
                                    if(!$result_requested_equipment_fetch){
                                        echo "There is some problem in fetching lab equipment data.";
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
                                        <td><?php echo $row['quantity'];?></td>
                                        <td><?php echo $eqrow['quantity'];?></td>
                                        <td><?php echo $eqrow['desc1'];?></td>
                                        <td><?php echo $eqrow['desc2'];?></td>
                                        <td><?php echo $row['id'];?></td>
                                        <td>
                                            <form action="lent.php" method="post" >
                                                <input type="text" name="dsrno" value="<?php echo $row['dsrno']; ?>" style="display:none;">
                                                <input type="text" name="labno" value="<?php echo $labno; ?>" style="display:none;">
                                                <input type="text" name="lendto" value="<?php echo $row['id']; ?>" style="display:none;">
                                                <input type="number" name="lendquan" id="lendquan" min ="1" max="<?php if($row['quantity']>$eqrow['quantity']) echo $eqrow['quantity']; else echo $row['quantity'];?>" style="width:150px;" placeholder="Lending Quantity" required>
                                                <button class="button1" type="submit" name="lend"> 
                                                    Lend
                                                </button>
                                            </form>
                                            <form action="lent.php" method="post">
                                                <input type="text" name="dsrno" value="<?php echo $row['dsrno']; ?>" style="display:none;">
                                                <input type="text" name="labno" value="<?php echo $labno; ?>" style="display:none;">
                                                <input type="text" name="lendto" value="<?php echo $row['id']; ?>" style="display:none;">
                                                
                                                <button class="button1" type="submit" name="deny"> 
                                                    Deny
                                                </button>
                                            </form>
                                        </td>
                                        </tr>
                                    <?php
                                    }
                                
                            ?>
                            
                        </tbody>
                    </table>
                </div>
        <?php 
        }
        ?>
</body>
</html>