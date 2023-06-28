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