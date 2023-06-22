<?php 
    session_start();
    //If a user is logged in and is a lab-assistant
    if (isset($_SESSION['logged']) && $_SESSION['role']=='lab-assistant') 
    {
        include 'connection.php';
        $id=$_SESSION['id'];

        if(isset($_POST['addeq']))
        {
            echo "ADDED";
            $eqname=$_POST['eqname'];
            $dsrno=$_POST['dsrno'];
            $quantity=$_POST['quantity'];
            echo $eqname;
            echo $dsrno;
            echo $quantity;

            // $sql1=mysqli_query($conn,"SELECT * FROM labs WHERE assistid=$id");
            // $row1 = mysqli_fetch_array($sql1,MYSQLI_ASSOC);
            // $labno=$row1['labno'];
            // $sql2=mysqli_query($conn,"INSERT INTO $labno(eqname,dsrno,quantity) values($eqname,$dsrno,$quantity)");
        }
    }
    //If a user is logged in and is not a lab-assistant
    else if (isset($_SESSION['logged']) && $_SESSION['role']!='lab-assistant')
    {
		$role=$_SESSION['role'];
		if($role=='admin')
			header('Location:../Admin/dash_admin.php');    
		else if($role=='student')
			header('Location:../Student/dash_student.php');    
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
</head>
<body>
    
    <a href='../logout.php'>SIGN OUT</a>
    <div class="row col-lg-12 card card-body table-responsive">
        <table class="table table-centered table-nowrap mb-0">
            <thead>
                <tr>
                    <!-- HEADINGS -->
                    <th scope="col">Equipment Name<br></th>
                    <th scope="col">DSR Number</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Update<br></th>
                </tr>
            </thead>
            
            <tbody>
            <tr>
                <form action="view_equ.php" method="post">
                <td><input type="text" name='eqname' id='eqname' required></td>
                <td><input type="text" name='dsrno' id='dsrno' required></td>
                <td><input type="number" name='quantity' id='quantity' required></td>

                <td>
                    <button class="button1" type="submit" name="addeq"> 
                        Add equi
                    </button>
                </td>
                </form>
            </tr>
            <?php
                
                $sql1=mysqli_query($conn,"SELECT * FROM labs WHERE assistid=$id");
                echo $id;
                $row1 = mysqli_fetch_array($sql1,MYSQLI_ASSOC);
                $labno=$row1['labno'];
                echo $labno;
                $table=mysqli_query($conn,"SELECT * FROM $labno");
                while($row = mysqli_fetch_array($table,MYSQLI_ASSOC))
                {
                    ?>
                    <tr>
                    <td><?php echo $row['eqname'];?></td>
                    <td><?php echo $row['dsrno'];?></td>
                    <td><?php echo $row['quantitiy'];?></td>
                    <td></td>
                    </tr>
                    <?php
                    
                }
            ?>
            

            </tbody>
        </table>
    
</body>
</html>