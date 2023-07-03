<?php 
    session_start();
    //If a user is logged in and is a lab-assistant
    if (isset($_SESSION['logged']) && $_SESSION['role']=='lab-assistant') 
    {
        // CONNECT DATABASE
        include('../connection.php');
        // USER ID
        $id=$_SESSION['id'];
        // IF ADDING EQUIPMENT
        if(isset($_POST['addeq']))
        {
            
            // GET DATA FROM FORM
            $eqname=$_POST['eqname'];
            $eqtype=$_POST['eqtype'];
            $dsrno=$_POST['dsrno'];
            $quantity=$_POST['quantity'];   
            $desc1=$_POST['desc1'];
            $desc2=$_POST['desc2'];
            $cost=$_POST['cost'];
            if($eqtype!=0)
            {
                //GET LAB-NUMBER FROM LAB TABLE USING SESSION ID (ASSISTANT ID)
                $sql1=mysqli_query($conn,"SELECT * FROM labs WHERE assistid=$id");
                
                $row1 = mysqli_fetch_array($sql1,MYSQLI_ASSOC);
                if(!$row1)
                {
                    echo mysqli_error($conn);
                    die();
                }
                
                $labno=$row1['labno'];   //LAB-NUMBER
                $dept=$row1['dept'];
                $dsr="KJSCE/".$dept."/".$labno."/".$dsrno;


                // echo $labno;
                // SELECT EQUIPMENT WITH SAME NAME AND SAME DSR-NUMBER
                $sql2=mysqli_query($conn,"SELECT * FROM $labno WHERE eqname='$eqname' AND dsrno='$dsr'");
                if(mysqli_num_rows($sql2)==0)
                {
                    // IF NO SAME EQUIPMENT WITH SAME NAME AND SAME DSR-NUMBER STORED EARLIER
                    if(mysqli_num_rows(mysqli_query($conn,"SELECT * FROM $labno WHERE dsrno='$dsr'"))==0)
                    {
                        mysqli_query($conn,"INSERT INTO $labno(eqname,eqtype,dsrno,quantity,desc1,desc2,cost) values('$eqname','$eqtype','$dsr',$quantity,'$desc1','$desc2','$cost')");
                    }
                    else 
                    {
                        // INVALID INPUT
                        // SAME DSR NUMBER DIFFERENT EQUIPMENT NAME
                    }
                }
                else 
                {
                    // SAME EQUIPMENT PRESENT, UPDATE QUANTITY 
                    $row2=mysqli_fetch_array($sql2,MYSQLI_ASSOC);
                    $qu=$row2['quantity'];  // OLD QUANTITY
                    mysqli_query($conn,"UPDATE $labno set quantity=($quantity+$qu) WHERE dsrno='$dsr'");
                }
            }
            header("Location:view_equ.php");
            
        }
        if(isset($_POST['lend']))
        {
            $dsrno=$_POST['dsrno']; 
            $curr_lab=$_POST['labno'];  //LEND FROM
            $lendquan=$_POST['lendquan'];   //LENDING QUANTITY
            $lendto=$_POST['lendto']; //LEND TO

            //FETCH EQUIPMENT DETAILS
            $fetch_equipment=mysqli_query($conn,"SELECT * FROM $curr_lab WHERE dsrno='$dsrno'");
            $eqrow=mysqli_fetch_array($fetch_equipment,MYSQLI_ASSOC);
            
            //STORE EQUIPMENT DETAILS
            $eqname=$eqrow['eqname'];
            $eqtype=$eqrow['eqtype'];
            $quantity=$eqrow['quantity'];
            $desc1=$eqrow['desc1'];
            $desc2=$eqrow['desc2'];
            $cost=$eqrow['cost'];

            $check_ownership=mysqli_query($conn,"SELECT * FROM lend WHERE dsrno='$dsrno' AND lendto='$curr_lab'");

            if(mysqli_num_rows($check_ownership)==1)    //IF EQUIPMENT IS NOT OWNED BY CURRENT LAB
            {
                /*
                    LAB-A : ORIGNAL OWNER OF EQUIPMENT
                    LAB-B : LENT EQUIPMNET FROM LAB-A
                    LAB-C : LENDING EQUIPMENT FROM LAB-B OWNED BY LAB-A
                */

                //FETCH OWNER USING 'lend' table
                $fetch_owner=mysqli_fetch_array($check_ownership,MYSQLI_ASSOC);
                $owner_lab=$fetch_owner['lendfrom'];    //labno OF LAB-A (OWNER OF EQUIPMENT)
                $orignal_lend_quan=$fetch_owner['lendquan'];    //lendquan BETWEEN LAB-A & LAB-B


                if($orignal_lend_quan==$lendquan)   //ALL EQUIPMENTS BEING LEND FROM LAB-B TO LAB-C (OWNED BY LAB-A)
                {
                    $check_prev_lend=mysqli_query($conn,"SELECT * FROM lend WHERE lendto='$lendto' AND dsrno='$dsrno' AND lendfrom='$owner_lab'");
                    if(mysqli_num_rows($check_prev_lend)==0)    //LAB-C NOT LENT SAME EQUIPMENT FROM LAB-A
                    {
                        //SHIFT 'lend' TRANSACTION 'lendto' FROM LAB-B TO LAB-C
                        $insert_transaction=mysqli_query($conn,"UPDATE lend SET lendto='$lendto' WHERE lendto='$curr_lab' AND dsrno='$dsrno' AND lendfrom='$owner_lab'");

                        //ADD EQUIPMENT IN LAB-C TABLE
                        $create_reciever_quantity=mysqli_query($conn,"INSERT INTO $lendto (eqname,dsrno,quantity,byquan,eqtype,desc1,desc2,cost) values('$eqname','$dsrno',$lendquan,$lendquan,'$eqtype','$desc1','$desc2',$cost)");

                    }
                    else    //LAB-C PREVIOUSLY LENT SAME EQUIPMENT FROM LAB-A
                    {
                        //UPDATE LEND TRANSACTION BETWEEN LAB-A & LAB-C
                        $insert_transaction=mysqli_query($conn,"UPDATE lend SET lendquan=(lendquan+$lendquan) WHERE lendto='$lendto' AND dsrno='$dsrno' AND lendfrom='$owner_lab'");
                        
                        //DELETE LEND TRANSACTION BETWEEN LAB-A & LAB-B
                        $delete_old_transaction=mysqli_query($conn,"DELETE FROM lend WHERE lendto='$curr_lab' AND dsrno='$dsrno'");
                        
                        //UPDATE EQUIPMENT QUANTITIES IN LAB-C TABLE
                        $update_reciever_quantity=mysqli_query($conn,"UPDATE $lendto SET byquan=(byquan+$lendquan),quantity=(quantity+$lendquan) WHERE dsrno='$dsrno'");
                        
                    }
                    //DELETE FROM LAB-B
                    $remove_old_lend=mysqli_query($conn,"DELETE FROM $curr_lab WHERE dsrno='$dsrno'");

                        
                }
                else    //SOME EQUIPMENTS BEING LEND TO LAB-C
                {
                    //CHECK IF LAB-C HAS LENT SAME EQUIPMENT FROM LAB-A
                    $check_prev_lend=mysqli_query($conn,"SELECT * FROM lend WHERE lendto='$lendto' AND dsrno='$dsrno' AND lendfrom='$owner_lab'");

                    if(mysqli_num_rows($check_prev_lend)==0)    //LAB-C NOT LENT SAME EQUIPMENT FROM LAB-A PREVIOUSLY
                    {                        
                        //LEND TABLE (BETWEEN LAB-A & LAB-C)
                        $insert_transaction=mysqli_query($conn,"INSERT INTO lend(lendfrom,dsrno,lendquan,lendto) values('$owner_lab','$dsrno',$lendquan,'$lendto')");
                        
                        //LAB-C TABLE
                        $create_reciever_quantity=mysqli_query($conn,"INSERT INTO $lendto (eqname,dsrno,quantity,byquan,eqtype,desc1,desc2,cost) values('$eqname','$dsrno',$lendquan,$lendquan,'$eqtype','$desc1','$desc2',$cost)");

                    }                    
                    else    //LAB-C PREVIOUSLY LENT SAME EQUIPMENT FROM LAB-A
                    {
                        //LEND TABLE (BETWEEN LAB-A & LAB-C)
                        $insert_transaction=mysqli_query($conn,"UPDATE lend SET lendquan=(lendquan+$lendquan) WHERE lendto='$lendto' AND dsrno='$dsrno' AND lendfrom='$owner_lab'");
                        
                        //LAB-C TABLE
                        $update_reciever_quantity=mysqli_query($conn,"UPDATE $lendto SET byquan=(byquan+$lendquan),quantity=(quantity+$lendquan) WHERE dsrno='$dsrno'");

                    }
                    
                    //SUBTRACT FROM LAB-B TABLE
                    $update_old_lend=mysqli_query($conn,"UPDATE $curr_lab SET quantity=quantity-$lendquan, byquan=byquan-$lendquan WHERE dsrno='$dsrno'");
                    
                    //LEND TABLE (BETWEEN LAB-A & LAB-B)                    
                    $modify_old=mysqli_query($conn,"UPDATE lend SET lendquan=(lendquan-$lendquan) WHERE lendto='$curr_lab' AND dsrno='$dsrno' AND lendfrom='$owner_lab'");
                
                }
            }
            else
            {
                //CHECK IF PREVIOUSLY LENT PRODUCT TO SAME LAB
                $check_prev_lend=mysqli_query($conn,"SELECT * FROM lend WHERE lendto='$lendto' AND dsrno='$dsrno' AND lendfrom='$curr_lab'");
                
                if(mysqli_num_rows($check_prev_lend)==0)    //NOT PREVIOUSLY LENT SAME PRODUCTS
                {
                    //NEW RECORD IN 'lend' TABLE
                    $insert_transaction=mysqli_query($conn,"INSERT into lend(lendfrom,dsrno,lendquan,lendto) VALUES('$curr_lab','$dsrno',$lendquan,'$lendto')");

                    //ADD EQUIPMENT IN LAB TABLE
                    $create_reciever_quantity=mysqli_query($conn,"INSERT INTO $lendto (eqname,dsrno,quantity,byquan,eqtype,desc1,desc2,cost) values('$eqname','$dsrno',$lendquan,$lendquan,'$eqtype','$desc1','$desc2',$cost)");
                }
                else    //PREVIOUSLY LENT SAME PRODUCT
                {
                    //UPDATE RECORD IN 'lend' TABLE
                    $insert_transaction=mysqli_query($conn,"UPDATE lend SET lendquan=(lendquan+$lendquan) WHERE lendto='$lendto' AND dsrno='$dsrno' AND lendfrom='$curr_lab'");

                    //UPDATE RECORD IN LAB TABLE
                    $update_reciever_quantity=mysqli_query($conn,"UPDATE $lendto SET byquan=(byquan+$lendquan),quantity=(quantity+$lendquan) WHERE dsrno='$dsrno'");                
                }
                
                //UPDATE LENDER QUANTITIES
                $update_lender_quantity=mysqli_query($conn,"UPDATE $curr_lab SET toquan=(toquan+$lendquan), quantity=(quantity-$lendquan) WHERE eqname='$eqname' AND dsrno='$dsrno'");                    
            }
            header("Location:view_equ.php");
        }
        if(isset($_POST['delete'])) //IF DELETING EQUIPMENT
        {
            $dsrno=$_POST['dsrno'];
            $labno=$_POST['labno'];
            $sql1=mysqli_query($conn,"DELETE FROM $labno WHERE dsrno='$dsrno'");
            header("Location:view_equ.php");

        }
        if(isset($_POST['delete_lend'])) //IF DELETING LENT EQUIPMENT
        {
            $dsrno=$_POST['dsrno'];
            $labno=$_POST['labno'];
            $sql1=mysqli_query($conn,"UPDATE $labno SET quantity=0 WHERE dsrno='$dsrno'");
            header("Location:view_equ.php");

        }
        
        if(isset($_POST['return'])||isset($_POST['returnall']))     // IF RETURNING EQUIPMENT
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
            }
            else
            {
                $reduce_lend_table=mysqli_query($conn,"UPDATE lend SET lendquan=lendquan-$requan WHERE (lendto='$lendto' AND dsrno='$dsrno' AND lendfrom='$lendfrom')");
                $reduce_lend_this_lab = mysqli_query($conn,"UPDATE $lendto SET quantity=(quantity-$requan) ,byquan=(byquan-$requan) WHERE dsrno='$dsrno'");
            }
            // UPDATE VALUES IN ORIGINAL TABLE
            $remove_lendto = mysqli_query($conn,"  UPDATE $lendfrom SET toquan=(toquan-$requan), quantity=(quantity+$requan)WHERE dsrno='$dsrno'");
            header("Location:view_equ.php");

        }
       
        
        if (isset($_POST['update'])) {
            $eqname = $_POST['name'];
            $eqtype = $_POST['type'];
            $dsrno = $_POST['dsr'];
            $quantity = $_POST['quant'];
            $desc1 = $_POST['description1'];
            $desc2 = $_POST['description2'];
            $cost = $_POST['cost'];
      
            $sql1=mysqli_query($conn,"SELECT * FROM labs WHERE assistid=$id");
                    
            $row1 = mysqli_fetch_array($sql1,MYSQLI_ASSOC);
            if(!$row1)
            {
                echo mysqli_error($conn);
                die();
            }
            
            $labno=$row1['labno'];   //LAB-NUMBER
      
            $updateQuery = "UPDATE $labno SET eqname='$eqname', eqtype='$eqtype', dsrno='$dsrno', quantity='$quantity', desc1='$desc1', desc2='$desc2', cost='$cost' WHERE dsrno='$dsrno'";
            $result = mysqli_query($conn, $updateQuery);
      
            if ($result) {
                // Update successful
                echo "Update successful!";
            } else {
                // Update failed
                echo "Update failed: " . mysqli_error($conn);
            }
            header("Location:view_equ.php");

        }

        
    }
    //If a user is logged in and is not a lab-assistant
    else if (isset($_SESSION['logged']) && $_SESSION['role']!='lab-assistant')
    {
		$role=$_SESSION['role'];
		if($role=='admin')
			header('Location:../Admin/dash.php');    
		else if($role=='student')
			header('Location:../Student/dash.php');    
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
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"> -->
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script> --> 
    
    <script type="text/javascript" src="../js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="CSS/styles.css">
    <!-- using an offline copy saves time spent for loading bootstrap from online source  -->
    <style>
    .popup {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 400px;
      background-color: #f1f1f1;
      padding: 20px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    .form-group {
      margin-bottom: 15px;
    }

    .form-group label {
      display: block;
      font-weight: bold;
    }

    .form-group input,
    .form-group textarea {
      width: 100%;
      padding: 5px;
      border: 1px solid #ccc;
      border-radius: 3px;
    }

    .form-group textarea {
      height: 80px;
    }

    .form-group input[type="submit"] {
      background-color: #4CAF50;
      color: white;
      cursor: pointer;
    }

    .form-group input[type="submit"]:hover {
      background-color: #45a049;
    }

    .form-group button {
      background-color: #ccc;
      color: black;
      cursor: pointer;
    }

    .form-group button:hover {
      background-color: #999;
    }

    .search-container{
        display: flex;
        justify-content: center;
    }

    #search{
        width: 350px;
    }
  </style>
</head>
<body>
    <!-- TEMPORARY DASHBOARD -->
    <div>
        <button onclick="window.location.href='dash.php'"> 
            Dashboard
        </button>
        <button onclick="window.location.href='view_equ.php'"> 
            View Equipment
        </button>
        <button onclick="window.location.href='lent_equ.php'"> 
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
                <option value="quantity">Quantity</option>
                <option value="type">Type</option>
                <option value="cost">Cost</option>
            </select>
            <br>
            <button class="btn btn-primary" type="submit" value="Search">Submit</button>
        </form>
    </div>

    
    <!-- MAIN TABLE  -->
    <div class="row col-lg-12 card card-body table-responsive">
        <table class="table table-centered table-nowrap mb-0">
            <thead>
                <tr>
                    <!-- HEADINGS -->
                    <th scope="col">Name<br></th>
                    <th scope="col">Type<br></th>
                    <th scope="col">DSR</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Lent Quantity</th>
                    <th scope="col">Description 1</th>
                    <th scope="col">Description 2</th>
                    <th scope="col">Cost/Item</th>
                    <th scope="col">Update<br></th>
                </tr>
            </thead>
            
            <tbody>

                <tr>
                    <!-- FORM FOR INPUTTING EQUIPMENT  -->
                    <form action="view_equ.php" method="post">
                        <!-- placeholder helps when the table headers are not visible without scrolling to the top -->
                    <td><input type="text" name='eqname' id='eqname' placeholder="Enter Equipment Name" required></td>
                    <td>
                        <select id="eqtype" name="eqtype" placeholder="Equipment Type" required>
                            <option value="0" selected>None</option>
                            <option value="Software">Software</option>
                            <option value="Hardware">Hardware</option>
                            <option value="Furniture">Furniture</option>
                        </select>
                    </td>
                    <td><input type="text" name='dsrno' id='dsrno' placeholder="DSR No." required></td>
                    <td><input type="number" name='quantity' id='quantity' placeholder="Quantity" required min="1"></td>
                    <td></td>
                    <td><input type="text" name='desc1' placeholder="Description 1" id='desc1'></td>
                    <td><input type="text" name='desc2' placeholder="Description 2" id='desc2'></td>
                    <td><input type="number" step="0.01" name='cost' placeholder="Cost" id='cost'></td>

                    <td>
                        <button class="btn btn-outline-success" style="width: 80px;" type="submit" name="addeq"> 
                            Add
                        </button>
                    </td>
                    </form>
                </tr>
                <?php
                    $sql_lab_fetch = "SELECT *
                                      FROM labs
                                      WHERE assistid = $id";
                    $result_lab_fetch = mysqli_query($conn, $sql_lab_fetch);
                    if(!$result_lab_fetch){
                        echo "Lab details could not be fetched.";
                        return;
                    }
                    $lab_data = mysqli_fetch_array($result_lab_fetch, MYSQLI_ASSOC);
                    $labno = $lab_data['labno'];
                    echo "Here is your lab number: ";
                    echo $labno;
                    if(isset($_POST['search']))
                    {
                        $search = $_POST['search'];
                        $sql_equipment_fetch = "SELECT *
                                                FROM $labno
                                                WHERE (eqname LIKE '%$search%' OR
                                                        dsrno LIKE '%$search%' OR 
                                                        eqtype LIKE '%$search%' OR
                                                        quantity LIKE '%$search%' OR
                                                        desc1 LIKE '%$search%' OR
                                                        desc2 LIKE '%$search%' OR
                                                        cost LIKE '%$search%' OR
                                                        toquan LIKE '%$search%' OR
                                                        byquan LIKE '%$search%')";
                        $result_equipment_fetch = mysqli_query($conn, $sql_equipment_fetch);
                        if(!$result_equipment_fetch){
                            echo "There is some problem in fetching lab equipment data.";
                            return;
                        }
                    } else {
                        $sql_equipment_fetch = "SELECT *
                                                FROM $labno";
                        $result_equipment_fetch = mysqli_query($conn, $sql_equipment_fetch);
                        if(!$result_equipment_fetch){
                            echo "There is some problem in fetching lab equipment data.";
                            return;
                        }
                    }
                    
                    //FETCH LAB-NUMBER USING SESSION ID
                    $v=1;
                    while($row = mysqli_fetch_array($result_equipment_fetch, MYSQLI_ASSOC))
                    {
                        ?>
                        <tr>
                            <td><?php echo $row['eqname'];?></td>
                            <td><?php echo $row['eqtype'];?></td>
                            <td><?php echo $row['dsrno'];?></td>
                            <td><?php echo $row['quantity'];?></td>
                            <td><?php echo $row['toquan']?></td>
                            <td><?php echo $row['desc1'];?></td>
                            <td><?php echo $row['desc2'];?></td>
                            <td><?php echo $row['cost'];?></td>
                            <td>
                            <button type="submit" name="delete" style="width: 80px;" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#staticBackdroplend<?php echo str_replace('/', '_', strtolower($row['dsrno']));?>" <?php if($row['quantity']==0) echo "disabled";?>>
                                    Lend
                                </button>
                                <div class="modal fade" id="staticBackdroplend<?php echo str_replace('/', '_', strtolower($row['dsrno']));?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="staticBackdropLabel">Lending</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <?php
                                                    $dsrno=$row['dsrno'];
                                                    $fetch_equipment=mysqli_query($conn,"SELECT * FROM $labno WHERE dsrno='$dsrno'");
                                                    
                                                    $eqrow=mysqli_fetch_array($fetch_equipment,MYSQLI_ASSOC);
                                                    $eqtype=$eqrow['eqtype'];
                                                    $eqname=$eqrow['eqname'];
                                                    $quantity=$eqrow['quantity'];
                                                    
                                                    echo "Equipment Name: <strong>".$eqname."</strong><br>";
                                                    echo "Equipment Type: <strong>".$eqtype."</strong><br>";
                                                    echo "Equipment Type: <strong>".$dsrno."</strong><br>";
                                                    echo "Equipment Quantity: <strong>".$quantity."</strong><br><br>";
                                                
                                                ?>
                                                
                                                <form action="" method="post">  
                                                    <input type="text" name="labno" value="<?php echo $labno; ?>" style="display:none;">
                                                    <input type="text" name="dsrno" value="<?php echo $row['dsrno']; ?>" style="display:none;">              
                                                    
                                                    <div class="form-floating col-12">
                                                        <select class="form-select" id="lendto" name="lendto" required>
                                                            <option value="" disabled selected>Choose a Laboratory</option>
                                                            <?php 
                                                                $fetch_labs=mysqli_query($conn,"SELECT * FROM labs WHERE NOT labno='$labno' AND labno NOT IN (SELECT lendfrom FROM lend WHERE dsrno='$dsrno' AND lendto='$labno')");
                                                                while($lab_row=mysqli_fetch_array($fetch_labs,MYSQLI_ASSOC))
                                                                {
                                                                    $labname=$lab_row['labname'];
                                                                    $labnum=$lab_row['labno'];
                                                                    ?>
                                                                    <option value="<?php echo $labnum;?>" ><?php echo $labnum;?> - <?php echo $labname;?></option>
                                                                    <?php
                                                                }
                                                            ?>

                                                        </select>
                                                        <label for="lendto" class="select-label ms-3">Lend To</label>
                                                    </div>
                                                    <br>
                                                    <div class="form-floating col-12 mb-4">
                                                        <input class="form-control" type="number" name="lendquan" id="lendquan" min ="1" max="<?php echo $quantity;?>" required>
                                                        <label class="label ms-2" for="lendquan">Lending Quantity</label>        
                                                    </div>
                                                    <br>
                                                            
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn alert-danger" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" name="lend" class="btn btn-outline-success">Lend</button>
                                                                </form>
                                                            </div>
                                                            </div>
                                                            </div>
                                                            </div>

                            <?php 
                            if($row['byquan']==0)
                            {
                                ?>
                                    <button class="btn btn-success" onclick="openPopup(<?php echo$v;?>)"> 
                                        Update
                                    </button>
                                    <!-- Button trigger modal -->
                                    
                                    <button type="submit" name="delete" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#staticBackdropdelete<?php echo str_replace('/', '_', strtolower($row['dsrno']));?>" <?php if($row['quantity']==0) echo "disabled";?>>
                                        Delete
                                    </button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="staticBackdropdelete<?php echo str_replace('/', '_', strtolower($row['dsrno']));?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title text-danger" id="staticBackdropLabel">Warning</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <?php
                                                if($row['toquan']>0)
                                                {
                                                    echo "This equipment has been lent to other labs.<br>Are you sure you want to delete the equipment you have?<br>This action cannot be reversed<br>(This will not delete the equipments lent to others)";
                                                }
                                                else
                                                {
                                                    echo "Are you sure you want to permanently delete this equipment?<br>This action cannot be reversed";
                                                }
                                                    
                                            ?>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn alert-danger" data-bs-dismiss="modal">Cancel</button>
                                            <form action="" method="post">  
                                                <input type="text" name="labno" value="<?php echo $labno; ?>" style="display:none;">
                                                <input type="text" name="dsrno" value="<?php echo $row['dsrno']; ?>" style="display:none;">              
                                                <button type="submit" name="<?php if($row['toquan']>0) echo "delete_lend"; else echo "delete";?>" class="btn btn-danger">Delete</button>
                                            </form>
                                        </div>
                                        </div>
                                    </div>
                                    </div>
        
                                
                                <?php 
                            }
                            else 
                            {
                                ?>
                                <button name="return" style="width: 80px;" class="btn btn-outline-danger alert-danger" data-bs-toggle="modal" data-bs-target="#staticBackdropreturn<?php echo str_replace('/', '_', strtolower($row['dsrno']));?>">
                                            Return
                                        </button>
                                        <div class="modal fade" id="staticBackdropreturn<?php echo str_replace('/', '_', strtolower($row['dsrno']));?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="staticBackdropLabel">Returning</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <?php
                                                            $dsrno=$row['dsrno'];
                                                            $fetch_equipment=mysqli_query($conn,"SELECT * FROM $labno WHERE dsrno='$dsrno'");
                                                            $fetch_lab=mysqli_query($conn,"SELECT * FROM lend WHERE lendto='$labno' AND dsrno='$dsrno'");
                                                            if(!$fetch_equipment)
                                                            {
                                                                echo mysqli_error($conn);
                                                                die();
                                                            }
                                                            $labno_row=mysqli_fetch_array($fetch_lab,MYSQLI_ASSOC);
                                                            $lendfrom=$labno_row['lendfrom'];

                                                            $eqrow=mysqli_fetch_array($fetch_equipment,MYSQLI_ASSOC);
                                                            $eqtype=$eqrow['eqtype'];
                                                            $eqname=$eqrow['eqname'];
                                                            $quantity=$eqrow['quantity'];
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
                                        <?php
                                    
                            }
                            ?>
                            
                            </td>
                        </tr>
                        <?php
                        $v=$v+1;
                    }
                ?>
            </tbody>
        </table>
    </div>
    <?php 
    if(isset($_POST['delete']))
    {
        $dsrno=$_POST['dsrno'];
        ?>
            <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="false">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">r
                            <h1 class="modal-title fs-5" id="staticBackdropLabel">Modal title</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <?php echo $dsrno;?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary">Understood</button>
                        </div>
                    </div>
                </div>
            </div>

        <?php
    }
    
    ?>
    
    <div id="popup" class="popup">
    <h2>Update Form</h2>
    <form action="" method="post" id="updateForm">
      <div class="form-group">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name">
      </div>
      <div class="form-group">
        <label for="type">Type:</label>
        <input type="text" id="type" name="type">
      </div>
      <div class="form-group">
        <label for="dsr">DSR No.:</label>
        <input type="text" id="dsr" name="dsr">
      </div>
      <div class="form-group">
        <label for="quantity">Quantity:</label>
        <input type="number" id="quant" name="quant">
      </div>
      <div class="form-group">
        <label for="description1">Description 1:</label>
        <textarea id="description1" name="description1"></textarea>
      </div>
      <div class="form-group">
        <label for="description2">Description 2:</label>
        <textarea id="description2" name="description2"></textarea>
      </div>
      <div class="form-group">
        <label for="cost">Cost:</label>
        <input type="number" id="costin" name="cost">
      </div>
      <div class="form-group">
        <input type="submit" name="update" value="update">
      </div>
    </form>
    <button onclick="closePopup()">Close</button>
  </div>

  <script>
function openPopup(rowId) {
  var popup = document.getElementById("popup");
  popup.style.display = "block";

  // Get the data from the table row
  var tableRows = document.getElementsByTagName("tr");
  var targetRow = tableRows[rowId + 1]; // Add 1 to skip the form row
  var cells = targetRow.getElementsByTagName("td");

  // Populate the form fields with the data
  document.getElementById("name").value = cells[0].innerHTML;
  document.getElementById("type").value = cells[1].innerHTML;
  document.getElementById("dsr").value = cells[2].innerHTML;
  document.getElementById("quant").value = cells[3].innerText;
  document.getElementById("description1").value = cells[5].innerHTML;
  document.getElementById("description2").value = cells[6].innerHTML;
  document.getElementById("costin").value = cells[7].innerHTML;
}

function closePopup() {
      var popup = document.getElementById("popup");
      popup.style.display = "none";
    }
  </script>
</body>
</html>