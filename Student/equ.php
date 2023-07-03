<?php 
    session_start();
    //If a user is logged in and is a student
    if (isset($_SESSION['logged']) && $_SESSION['role']=='student') 
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
    //If a user is logged in and is not a student
    else if (isset($_SESSION['logged']) && $_SESSION['role']!='student')
    {
        $role=$_SESSION['role'];
        if($role=='admin')
            header('Location:../Admin/dash.php'); 
        else if($role=='lab-assistant')
            header('Location:../LabAssistant/dash.php');    
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
</head>
<body>
    <style>
        table{
            text-align: center;
        }
        </style>
    <div>
        <button onclick="window.location.href='dash.php'"> 
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
    <?php
        $result_table_display = mysqli_query($conn,"SELECT * FROM lend WHERE lendto='$id'");
        if(!$result_table_display)
        {
            echo "Error in fetching lending data";
            return;
        }
        else
        {
            if(mysqli_num_rows($result_table_display)>0)
            {
                ?>
                    <h4 style="text-align: center;">Equipment Lended</h4>
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
                                    <th scope="col">Lab</th>
                                    <th scope="col">Return</th>
                                    <!-- <th scope="col">View<br></th> -->
                                </tr>
                            </thead>
                            <?php
                                    // $parts = parse_url(basename($_SERVER['REQUEST_URI']));
                                    
                                    
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
                                                <input type="number" name="requan" id="requan" min ="1" max="<?php echo $row['lendquan'];?>" style="width:150px;" placeholder="Return quantity" required>                                
                                                <button class="button1" type="submit" name="return"> 
                                                    Return
                                                </button>
                                            </td>
                                        </form>
                                    </tr>
                                    <?php
                                }
                            ?>
                        </table>
                    </div>
                    
                <?php
            }
        }
    ?>
    
    
    <?php
        $result_table_display = mysqli_query($conn,"SELECT * FROM request WHERE id=$id");
        if(!$result_table_display)
        {
            echo "Error in fetching requests data";
            return;
        }
        else
        {
            if(mysqli_num_rows($result_table_display)>0)
            {
                ?>
                <h4 style="text-align: center;">Requests</h4>
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
                                    <th scope="col">Lab</th>
                                    <th scope="col">Return</th>
                                    <!-- <th scope="col">View<br></th> -->
                                </tr>
                            </thead>
                            <?php                                   
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
                                                <td><?php echo $row['quantity']?></td>
                                                <td><?php echo $eqrow['desc1'] ?></td>
                                                <td> <?php echo $eqrow['desc2'] ?> </td>
                                                <td> <?php echo $row['labno'] ?> </td>
                                                
                                                <form action="equ.php" method="post">
                                                <input type="text" name="dsrno" value="<?php echo $row['dsrno']; ?>" style="display:none;">
                                                <input type="text" name="labno" value="<?php echo $labno; ?>" style="display:none;">
                                                <td>
                                                    <button class="button1" type="submit" name="delete"> 
                                                        Delete Request
                                                    </button>
                                                </td>  
                                            </form>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                        </table>
                    </div>
                <?php
            }
        }
    ?>
    
    
    </body>
</html>







