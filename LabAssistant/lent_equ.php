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
        if(isset($_POST['return']))
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
            if($requan==$lendquan)
            {
                //DELETE FROM LEND TABLE
                $remove_lend=mysqli_query($conn,"DELETE FROM lend WHERE lendto='$lendto' AND dsrno='$dsrno' AND lendfrom='$lendfrom'");
                if(!$remove_lend)
                {
                    echo "ERR1";
                    echo mysqli_error($conn);
                    die();
                }
                else
                {
                    //DELETE FROM TABLE OF RETURNING LAB
                    $remove_lendfrom = mysqli_query($conn," DELETE FROM $lendto WHERE dsrno='$dsrno'");
                    if(!$remove_lendfrom)
                    {
                        echo "ERR1";
                        echo mysqli_error($conn);
                        die();
                    }
                }
            }
            else
            {
                echo $lendfrom;
                echo $dsrno;
                $reduce_lend_table=mysqli_query($conn,"UPDATE lend SET lendquan=lendquan-$requan WHERE (lendto='$lendto' AND dsrno='$dsrno' AND lendfrom='$lendfrom')");
                if(!$reduce_lend_table)
                {
                    echo "ERR1";
                    echo mysqli_error($conn);
                    die();
                }
                else
                {
                    $reduce_lend_this_lab = mysqli_query($conn,"UPDATE $lendto SET quantity=(quantity-$requan) ,byquan=(byquan-$requan) WHERE dsrno='$dsrno'");
                    if(!$reduce_lend_this_lab)
                    {
                        echo "ERR1";
                        echo mysqli_error($conn);
                        die();
                    }
                }
            }
            
                
                
                // UPDATE VALUES IN ORIGINAL TABLE
                $remove_lendto1 = mysqli_query($conn,"  UPDATE $lendfrom 
                                                        SET toquan=(toquan-$requan), quantity=(quantity+$requan)
                                                        WHERE dsrno='$dsrno'");

                // $remove_lendto2 = mysqli_query($conn,"  UPDATE $lendfrom 
                //                                         SET quantity=(quantity+$requan) 
                //                                         WHERE dsrno='$dsrno'");

                
                if(!$remove_lendto1)
                {
                    echo "Error 1 in updating from original lab's table<br>";
                    echo mysqli_error($conn);
                    die();
                }
                // if(!$remove_lendto2)
                // {
                //     echo "Error 2 in updating from original lab's table<br>";
                //     echo mysqli_error($conn);
                //     die();
                // }
            
        }
        if(isset($_POST['lend']))
        {
            //FETCH FORM DATA
            $lendfrom=$_POST['labno'];
            $dsrno=$_POST['dsrno'];
            $lendquan=$_POST['lendquan'];
            $lendto=$_POST['lendto'];
            
            //FETCH SPECIFIC EQUIPMENT DATA FROM LAB TABLE
            $fetch_equipment=mysqli_query($conn,"SELECT * FROM $lendfrom WHERE dsrno='$dsrno'");
            if(!$fetch_equipment)   //Error in fetching equipment data
            {
                echo mysqli_error($conn);
                die();
            }
            else
            {
                //CHECK IF SAME EQUIPMENT HAS BEEN LENT TO SAME STUDENT/PROFESSOR
                $check_prev_lend=mysqli_query($conn,"SELECT * FROM lend WHERE lendfrom='$lendfrom' AND dsrno='$dsrno' AND lendto='$lendto'");
                if(!$check_prev_lend)   //error in executing query
                {
                    echo "Error in checking previous lending transaction<br>";
                    echo mysqli_error($conn);
                    die();
                }
                else
                {
                    if(mysqli_num_rows($check_prev_lend)==1)
                    {
                        $lend_transaction=mysqli_query($conn,"UPDATE $lendfrom SET toquan=toquan+$lendquan,quantity=quantity-$lendquan WHERE dsrno='$dsrno'");
                        if(!$lend_transaction)    //error in executing query
                        {
                            echo "Error in updating new lending transaction in lab table<br>";
                            echo mysqli_error($conn);
                            die();
                        }
                        $update_previous=mysqli_query($conn,"UPDATE lend SET lendquan=lendquan+$lendquan WHERE lendfrom='$lendfrom' AND dsrno='$dsrno' AND lendto='$lendto'");
                        if(!$update_previous)    //error in executing query
                        {
                            echo "Error in updating new lending transaction in lend table<br>";
                            echo mysqli_error($conn);
                            die();
                        }
                        $delete_request=mysqli_query($conn,"DELETE FROM request WHERE (labno='$lendfrom' AND dsrno='$dsrno' AND id='$lendto') ");
                        if(!$delete_request)    //error in executing query
                        {
                            echo "Error in inserting new lending transaction<br>";
                            echo mysqli_error($conn);
                            die();
                        }
                    }
                    else
                    {
                        $lend_transaction=mysqli_query($conn,"UPDATE $lendfrom SET toquan=toquan+$lendquan,quantity=quantity-$lendquan WHERE dsrno='$dsrno'");
                        if(!$lend_transaction)    //error in executing query
                        {
                            echo "Error in updating new lending transaction in lab table<br>";
                            echo mysqli_error($conn);
                            die();
                        }
                        $insert_transaction=mysqli_query($conn,"INSERT INTO lend(lendfrom,dsrno,lendto,lendquan) VALUES('$lendfrom','$dsrno',$lendto,$lendquan) ");
                        if(!$insert_transaction)    //error in executing query
                        {
                            echo "Error in inserting new lending transaction<br>";
                            echo mysqli_error($conn);
                            die();
                        }
                        else
                        {
                            $delete_request=mysqli_query($conn,"DELETE FROM request WHERE (labno='$lendfrom' AND dsrno='$dsrno' AND id='$lendto') ");
                            if(!$delete_request)    //error in executing query
                            {
                                echo "Error in inserting new lending transaction<br>";
                                echo mysqli_error($conn);
                                die();
                            }
                        }
                    }
                }
                

            }
                

        }
        if(isset($_POST['deny']))
        {
            $lendfrom=$_POST['labno'];
            $dsrno=$_POST['dsrno'];
            $lendto=$_POST['lendto'];
            $deny_request=mysqli_query($conn,"DELETE FROM request WHERE labno='$lendfrom' AND dsrno='$dsrno' AND id='$lendto'");
            if(!$deny_request)    //error in executing query
            {
                echo "Error in inserting new lending transaction<br>";
                echo mysqli_error($conn);
                die();
            }
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
    <link rel="stylesheet" href="CSS/styles.css">
    <!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">-->
    <link rel="stylesheet" href="../CSS/bootstrap.min.css">
    <!-- using an offline copy saves time spent for loading bootstrap from online source  -->

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

    <!-- MAIN TABLE  -->
    <?php
    //FETCH LAB-NUMBER USING SESSION ID
    $sql1=mysqli_query($conn,"SELECT * FROM labs WHERE assistid=$id");
    $row1 = mysqli_fetch_array($sql1,MYSQLI_ASSOC);
    $labno=$row1['labno'];

    //FETCH LAB TABLE USING LAB-NUMBER
    $table=mysqli_query($conn,"SELECT * FROM $labno");
    $lend=mysqli_query($conn,"SELECT * FROM lend where lendfrom='$labno'");
    if(mysqli_num_rows($lend)>0)
    {
        ?>
        <h4 style="text-align: center;">EQUIPMENTS LENT TO OTHERS</h4>
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
                        <th scope="col">Update<br></th>
                    </tr>
                </thead>
                
                <tbody>
                    <?php 
                        //FETCH LENDING DATA FOR THIS LAB
                        
                        while($row = mysqli_fetch_array($lend,MYSQLI_ASSOC))
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

    $lend=mysqli_query($conn,"SELECT * FROM lend where lendto='$labno'");
    if(mysqli_num_rows($lend)>0)
    {
    ?>
        <h4 style="text-align: center;">EQUIPMENTS LENT FROM OTHERS</h4>
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
                        while($row = mysqli_fetch_array($lend,MYSQLI_ASSOC))
                        {
                                                    
                            $lendfrom=$row['lendfrom'];
                            $dsrno=$row['dsrno'];
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
                                <td><form action="lent_equ.php" method="post">
                                    <input type="text" name="labno" value="<?php echo $labno; ?>" style="display:none;">
                                    <input type="text" name="dsrno" value="<?php echo $row['dsrno']; ?>" style="display:none;">
                                    <input type="number" name="requan" id="requan" min ="1" max="<?php echo $row['quantity'];?>" style="width:150px;" pplaceholder="Return quantity" required>
                                    <button class="button1" type="submit" name="return"> 
                                        Return
                                    </button>
                                </form></td>
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
    
    <?php
    $request=mysqli_query($conn,"SELECT * FROM request WHERE labno='$labno'");
    if(mysqli_num_rows($request)>0)
    {
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
                        
                        while($row = mysqli_fetch_array($request,MYSQLI_ASSOC))
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
                                    <form action="lent_equ.php" method="post" >
                                        <input type="text" name="dsrno" value="<?php echo $row['dsrno']; ?>" style="display:none;">
                                        <input type="text" name="labno" value="<?php echo $labno; ?>" style="display:none;">
                                        <input type="text" name="lendto" value="<?php echo $row['id']; ?>" style="display:none;">
                                        <input type="number" name="lendquan" id="lendquan" min ="1" max="<?php echo $row['quantity'];?>" style="width:150px;" placeholder="Lending Quantity" required>
                                        <button class="button1" type="submit" name="lend"> 
                                            Lend
                                        </button>
                                    </form>
                                    <form action="lent_equ.php" method="post">
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
    } ?>
</body>
</html>