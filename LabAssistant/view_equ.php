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
                        mysqli_query($conn,"INSERT INTO $labno(eqname,eqtype,dsrno,quantity,desc1,desc2,cost,byquan) values('$eqname','$eqtype','$dsr',$quantity,'$desc1','$desc2','$cost',0)");
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
                    mysqli_query($conn,"UPDATE $labno set quantity=($quantity+$qu) where eqname='$eqname' AND dsrno='$dsrno'");
                }
            }
            
        }
        if(isset($_POST['delete'])) //IF DELETING EQUIPMENT
        {
            // $eqname=$_POST['eqname'];
            $dsrno=$_POST['dsrno'];
            $sql1=mysqli_query($conn,"SELECT * FROM labs WHERE assistid=$id");
            $row1 = mysqli_fetch_array($sql1,MYSQLI_ASSOC);
            $labno=$row1['labno'];
            $sql1=mysqli_query($conn,"DELETE FROM $labno WHERE dsrno='$dsrno'");
        }
        if(isset($_POST['return']))
        {
            $lendto=$_POST['labno'];
            $dsrno=$_POST['dsrno'];

            //FIND LENDING LAB DETAILS
            $query=mysqli_query($conn,"SELECT * FROM lend WHERE lendto='$lendto' AND dsrno='$dsrno'");
            $row=mysqli_fetch_array($query,MYSQLI_ASSOC);
            $lendfrom=$row['lendfrom'];
            $lendquan=$row['lendquan'];
            $remove_lend=mysqli_query($conn,"DELETE FROM lend WHERE lendto='$lendto' AND dsrno='$dsrno' AND lendfrom='$lendfrom'");
            if(!$remove_lend)
            {
                echo "ERR1";
                echo mysqli_error($conn);
                die();
            }
            else
            {
                $remove_lendfrom=mysqli_query($conn,"DELETE FROM $lendto WHERE dsrno='$dsrno'");
                $remove_lendto1=mysqli_query($conn,"UPDATE $lendfrom SET toquan=0 WHERE dsrno='$dsrno'");
                $remove_lendto2=mysqli_query($conn,"UPDATE $lendfrom SET quantity=(quantity+$lendquan) WHERE dsrno='$dsrno'");
                if(!$remove_lendfrom)
                {
                    echo "ERR2";
                    echo mysqli_error($conn);
                    die();
                }
                if(!$remove_lendto1)
                {
                    echo "ERR3";
                    echo mysqli_error($conn);
                    die();
                }
                if(!$remove_lendto2)
                {
                    echo "ERR4";
                    echo mysqli_error($conn);
                    die();
                }
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
    <!-- Search bar -->
    
    <form action="" method="post" style="text-align:center;">
        <input type="text" name="search" id="search" style="text-align:center;" placeholder="Enter equipment which you want to search for">
        <br>
        <input type="submit" value="Search">
    </form>
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
                    <th scope="col">Description 1</th>
                    <th scope="col">Description 2</th>
                    <th scope="col">Cost</th>
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
                            <option value="0" selected>Other</option>
                            <option value="Software">Software</option>
                            <option value="Hardware">Hardware</option>
                            <option value="Furniture">Furniture</option>
                        </select>
                    </td>
                    <td><input type="text" name='dsrno' id='dsrno' placeholder="DSR No." required></td>
                    <td><input type="number" name='quantity' id='quantity' placeholder="Quantity" required></td>
                    <td><input type="text" name='desc1' placeholder="Description 1" id='desc1'></td>
                    <td><input type="text" name='desc2' placeholder="Description 2" id='desc2'></td>
                    <td><input type="number" step="0.01" name='cost' placeholder="Cost" id='cost'></td>

                    <td>
                        <button class="button1" type="submit" name="addeq"> 
                            Add
                        </button>
                    </td>
                    </form>
                </tr>
                <?php
                    //FETCH LAB-NUMBER USING SESSION ID
                    $sql1=mysqli_query($conn,"SELECT * FROM labs WHERE assistid=$id");
                    $row1 = mysqli_fetch_array($sql1,MYSQLI_ASSOC);
                    $labno=$row1['labno'];

                    //FETCH LAB TABLE USING LAB-NUMBER
                    $table=mysqli_query($conn,"SELECT * FROM $labno");

                    while($row = mysqli_fetch_array($table,MYSQLI_ASSOC))
                    {
                        ?>
                        <tr>
                            <td><?php echo $row['eqname'];?></td>
                            <td><?php echo $row['eqtype'];?></td>
                            <td><?php echo $row['dsrno'];?></td>
                            <td><?php echo $row['quantity'];?></td>
                            <td><?php echo $row['desc1'];?></td>
                            <td><?php echo $row['desc2'];?></td>
                            <td><?php echo $row['cost'];?></td>
                            <td>
                            <?php 
                            if($row['byquan']==0)
                            {
                                ?>
                                <form action="view_equ.php" method="post">
                                    <input type="text" name="dsrno" value="<?php echo $row['dsrno']; ?>" style="display:none;">
                                    <button class="button1" type="submit" name="update"> 
                                        Update
                                    </button>
                                    <button class="button1" type="submit" name="delete"> 
                                        Delete
                                    </button>
                                </form>
                                <form action="lend.php" method="post">
                                    <input type="text" name="dsrno" value="<?php echo $row['dsrno']; ?>" style="display:none;">
                                    <input type="text" name="labno" value="<?php echo $labno; ?>" style="display:none;">
                                    <button class="button1" type="submit" name="lend"> 
                                        Lend
                                    </button>
                                </form>
                                <?php 
                            }
                            else 
                            {
                                ?>
                                <form action="view_equ.php" method="post">
                                    <input type="text" name="labno" value="<?php echo $labno; ?>" style="display:none;">
                                    <input type="text" name="dsrno" value="<?php echo $row['dsrno']; ?>" style="display:none;">
                                    <button class="button1" type="submit" name="return"> 
                                        Return
                                    </button>
                                </form>
                                <?php
                            }
                            ?>
                            </td>
                        </tr>
                        <?php
                    }
                ?>
            </tbody>
        </table>
    </div>

</body>
</html>