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
            $_SESSION['returned_data'] = $_POST;
            
            // Redirect back to the page with the modal
            header('Location: your_page_with_modal.php');
            exit(); // Important to prevent further code execution
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
        exit(); // Important to prevent further code execution
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" /><!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">-->
    <!-- <link rel="stylesheet" href="../CSS/bootstrap.min.css"> -->
    <!-- using an offline copy saves time spent for loading bootstrap from online source  -->
    <link rel="stylesheet" href="./CSS/styles.css">
</head>
<body style="background-color: #f8f9fc;overflow-x: hidden;">
    <?php include('../Components/sidebar.php') ?>
    <div class="position-absolute container row w-100 top-0 ms-4" style="left: 100px; z-index:100;">

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
                <div class="col-md-1 pe-0 mt-1">
                    <label for="filter" class="form-label">Filter</label>
                </div>
                <div class="col-md-2 ps-0">
                    <select id="filter" name="filter" class="form-select">
                        <option value="" selected>None</option>
                        <option value="1">Search from Lended Equipments</option>
                        <option value="2">Search from Requested Equipments</option>
                        <option value="3">Search from All Equipmenrs</option>
                    </select>         
                </div>
                <div class="col-md-1 pe-0">
                <input class="btn btn-outline-danger alert-danger" type="submit" value="Search"><br><br>
                </div>
            </div>
        </form>
    </div>
    <?php
        $f_id = $_POST['filter'] ?? '';
        if($f_id == '' || $f_id == '1' || $f_id == '3')
        {
            ?>
                <h4 style="text-align: center; margin-right:50px;">Equipment Borrowed</h4>
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
                                <th scope="col">Return Quantity</th>
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
                                                <input type="number" class="col-sm-1 form-control" name="requan" id="requan" min ="1" max="<?php echo $row['lendquan'];?>"required>                                
                                            </td>
                                            <td>
                                                <input class="col-sm-1 btn btn-outline-dark form-control" type="submit" name="return" value="Return"> 
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
                
            <?php
        }
    ?>
    
    
    <?php
        if($f_id == '' || $f_id == '2' || $f_id == '3')
        {
            ?>
            <h4 style="text-align: center; margin-right:50px;">Requests</h4>
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
                                                            quantity LIKE '%$search%')";
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
                                        <td><?php echo $row['quantity']?></td>
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
            <?php
        }
    ?>
    </div>
    
    </body>
</html>
<!-- Your modal dialog box -->
<div class="modal" id="returnModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <!-- Display the returned form data here -->
                <?php if (isset($_SESSION['returned_data'])): ?>
                    <p>Returned Data:</p>
                    <pre><?php print_r($_SESSION['returned_data']); ?></pre>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript code to show the modal when necessary -->
<script>
    // Show the modal if the session variable is set
    if (<?php echo isset($_SESSION['returned_data']) ? 'true' : 'false'; ?>) {
        // Assuming you're using Bootstrap's modal component
        $('#returnModal').modal('show');
    }
</script>