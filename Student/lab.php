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
        if(isset($_POST['request']))
        {

            $dsrno=$_POST['dsrno']; 
            $labno=$_POST['labno'];  //REQUEST FROM LAB
            $quantity=$_POST['quan'];   //REQUESTING QUANTITY
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
                else 
                {
                    echo "REQUEST PRESENT";
                }
            }
        }
        if(isset($_POST['delrequest']))
        {

            $dsrno=$_POST['dsrno']; 
            $labno=$_POST['labno'];  //REQUEST FROM LAB
            $quantity=$_POST['quan'];   //REQUESTING QUANTITY
            $fetch_equipment=mysqli_query($conn,"DELETE FROM request WHERE labno='$labno' AND id=$id AND dsrno='$dsrno'");
            if(!$fetch_equipment)
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
        <button onclick="window.location.href='../logout.php'"> 
           Signout
        </button>
    </div>
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
                    <!-- <th scope="col">View<br></th> -->
                </tr>
            </thead>
            <?php
                    // $parts = parse_url(basename($_SERVER['REQUEST_URI']));
                    
                    $sql_table_display = "SELECT * 
                                        FROM $labno";
                    $result_table_display = mysqli_query($conn, $sql_table_display);
                    if(!$result_table_display){
                        echo "There is some problem in the connection.";
                        return;
                    }
                    
                    $num = mysqli_num_rows($result_table_display);
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
                                    $fetch_requested=mysqli_query($conn,"SELECT quantity FROM request WHERE dsrno='$dsrno' AND labno='$labno' AND id=$id");
                                    $quan=mysqli_fetch_array($fetch_requested,MYSQLI_ASSOC);
                                    if(mysqli_num_rows($fetch_requested)==1)
                                    {
                                        ?>
                                        <td><?php echo $quan['quantity'];?></td>
                                        <td>
                                            <button class="button1" name="delrequest">
                                                Delete Request
                                            </button>
                                        </td>
                                        <?php 
                                    }
                                    else if(mysqli_num_rows($fetch_requested)==0)
                                    {
                                        ?>
                                        <td><input type="number" name="quan"></td>
                                        <td>
                                            <button class="button1" name="request">
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
            <tbody>
    </body>
</html>







