<?php 
    session_start();
    //If a user is logged in and is a lab-assistant
    if (isset($_SESSION['logged']) && $_SESSION['role']=='lab-assistant') 
    {
        include '../connection.php';
        $id=$_SESSION['id'];
        $status=$_SESSION['status'];
        $labno=$_SESSION['labno'] ?? '';
        $temp_labno2_var = $_SESSION['labno2'] ?? '';
        if($labno==$temp_labno2_var)
            $lab2=$_SESSION['labno1'] ?? '';
        else
            $lab2=$_SESSION['labno2'] ?? '';
            
        if(isset($_POST['switch']))
        {
            if($labno==$_SESSION['labno2'])
                $_SESSION['labno']=$_SESSION['labno1'];
            else
                $_SESSION['labno']=$_SESSION['labno2'];

            $labno=$_SESSION['labno'];
            header("Location:index.php");
        }
        $sql1=mysqli_query($conn,"SELECT * FROM labs WHERE labno='$labno'");
        $row = mysqli_fetch_array($sql1,MYSQLI_ASSOC);
        $labname=$row['labname'];
        
        $fetch_equipment=mysqli_query($conn,"SELECT COUNT(quantity) AS num_equ, SUM(quantity) AS sum_equ, SUM(cost*quantity) AS total_cost FROM $labno");
        $fetch=mysqli_fetch_assoc($fetch_equipment);
        $num_equ=$fetch['num_equ'];
        $sum_equ=$fetch['sum_equ'];
        $total_cost=$fetch['total_cost'];

        $lending_data=mysqli_query($conn,"SELECT SUM(lendquan) AS sum_lend FROM lend WHERE lendfrom='$labno'");
        $fetch_lend=mysqli_fetch_assoc($lending_data);
        $sum_lend=$fetch_lend['sum_lend'];
        
        $borrowing_data=mysqli_query($conn,"SELECT SUM(lendquan) AS sum_borrow FROM lend WHERE lendto='$labno'");
        $fetch_borrow=mysqli_fetch_assoc($borrowing_data);
        $sum_borrow=$fetch_borrow['sum_borrow'];
        
        $requesting_data=mysqli_query($conn,"SELECT COUNT(labno) AS sum_request FROM request WHERE labno='$labno'");
        $fetch_request=mysqli_fetch_assoc($requesting_data);
        $sum_request=$fetch_request['sum_request'];
        

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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.1"> -->
    <!-- <link rel="stylesheet" type="text/css" href="../CSS/bootstrap.min.css"> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="./CSS/styles.css">
    <title>IM-KJSCE</title>
</head>
<body style="background-color: #f8f9fc;overflow-x: hidden;">
    <?php 
    include('../Components/sidebar.php') 
    ?>
    <?php
        function moneyFormatIndia($num) {
            $explrestunits = "" ;
            if(strlen($num)>3) {
                $lastthree = substr($num, strlen($num)-3, strlen($num));
                $restunits = substr($num, 0, strlen($num)-3); // extracts the last three digits
                $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
                $expunit = str_split($restunits, 2);
                for($i=0; $i<sizeof($expunit); $i++) {
                    // creates each of the 2's group and adds a comma to the end
                    if($i==0) {
                        $explrestunits .= (int)$expunit[$i].","; // if is first value , convert into integer
                    } else {
                        $explrestunits .= $expunit[$i].",";
                    }
                }
                $thecash = $explrestunits.$lastthree;
            } else {
                $thecash = $num;
            }
            return $thecash; // writes the final format where $currency is the currency symbol.
        }
    ?>
 	<div class="position-absolute row pe-4 top-0 mx-4" style="left: 100px; width: calc(100% - 100px);">
        <div class="h2 mt-4"><?php echo $labno ;?> - <u><?php echo $labname ;?></u></div>
        <div style="font-size:17px"><?php echo $dept; ?></div>
        <!-- <hr class="mt-4 shadow mx-5"> -->
        <div class="col-lg-4 col-md-6 mt-4 mb-2" onclick="window.open('view.php','_self')">
            <div class="card border-success border-5 border-end-0 border-top-0 border-bottom-0 rounded shadow-lg h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="card-head text-success text-uppercase mb-1">
                                Equipment Variety</div>
                            <div class="h4 card-content mb-0 text-dark"><?php if($num_equ>0) echo $num_equ ; else echo 0;?></div>
                        </div>
                        <div class="col-auto me-2">
                            <i class="fa-solid fa-boxes-stacked fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mt-4 mb-2" onclick="window.open('view.php','_self')">
            <div class="card border-success border-5 border-end-0 border-top-0 border-bottom-0 rounded shadow-lg h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="card-head text-success text-uppercase mb-1">
                                Equipment Quantity</div>
                            <div class="h4 card-content mb-0 text-dark"><?php if($sum_equ>0) echo $sum_equ ; else echo 0;?></div>
                        </div>
                        <div class="col-auto me-2">
                            <i class="fa-solid fa-box-open fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mt-4 mb-2" onclick="window.open('view.php','_self')">
            <div class="card border-success border-5 border-end-0 border-top-0 border-bottom-0 rounded shadow-lg h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="card-head text-success text-uppercase mb-1">
                                Total Inventory Cost</div>
                            <div class="h4 card-content mb-0 text-dark">&#8377; <?php if($total_cost>0) echo moneyFormatIndia($total_cost); else echo 0;?></div>
                        </div>
                        <div class="col-auto me-2">
                            <i class="fa-solid fa-indian-rupee-sign fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr class="mt-4 shadow mx-4">
        <div class="col-lg-4 col-md-6 mt-4 mb-2" onclick="window.open('lent.php','_self')">
            <div class="card border-primary border-5 border-end-0 border-top-0 border-bottom-0 rounded shadow-lg h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="card-head text-primary text-uppercase mb-1">
                                Lent Equipment</div>
                            <div class="h4 card-content mb-0 text-dark"><?php if($sum_lend>0) echo $sum_lend; else echo 0;?></div>
                        </div>
                        <div class="col-auto me-2">
                            <i class="fa-solid fa-cart-shopping fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mt-4 mb-2" onclick="window.open('lent.php','_self')">
            <div class="card border-primary border-5 border-end-0 border-top-0 border-bottom-0 rounded shadow-lg h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="card-head text-primary text-uppercase mb-1">
                                Borrowed Equipment</div>
                            <div class="h4 card-content mb-0 text-dark"><?php if($sum_borrow>0) echo $sum_borrow; else echo 0;?></div>
                        </div>
                        <div class="col-auto me-2">
                            <i class="fa-solid fa-cart-arrow-down fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mt-4 mb-2" onclick="window.open('lent.php','_self')">
            <div class="card border-primary border-5 border-end-0 border-top-0 border-bottom-0 rounded shadow-lg h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="card-head text-primary text-uppercase mb-1">
                                Lend Requests</div>
                            <div class="h4 card-content mb-0 text-dark"><?php if($sum_request>0) echo $sum_request; else echo 0;?></div>
                        </div>
                        <div class="col-auto me-2">
                            <i class="fa-solid fa-cart-plus fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php 
        if($status>2)
        {
        ?>
            <div class="position-absolute" style="top: 2rem; right: 3rem; z-index: 1000;">
                <form action="" method="post">
                    <button class="btn btn-outline-danger" type="submit" name="switch">SWITCH TO: <?php echo $lab2; ?></button>
                </form>
            </div>
            <?php 
        } 
    ?>
        <!-- <div class="position-absolute" id="report" style="bottom: 2rem; right: 3rem; z-index: 1000;;" onmouseenter="butExp()" onmouseleave="butCol() " onclick="window.print()"> -->
    <div class="position-absolute" id="report" style="bottom: 2rem; right: 3rem; z-index: 1000;;" onmouseenter="butExp()" onmouseleave="butCol()">
        <a href="report.php" id="reportlink" class="btn btn-danger rounded-circle shadow p-3">
        <span class="buttontext buttontext1" style="float:left; padding-right: 0.75em; font-weight: bold;">Generate<br>Report</span>
        <span style="float:right;">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-file-earmark-bar-graph" viewBox="0 0 16 16">
        <path d="M10 13.5a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-6a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v6zm-2.5.5a.5.5 0 0 1-.5-.5v-4a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-.5.5h-1zm-3 0a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5h-1z"/>
        <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
        </svg>
        </span>
    </div>
    
    <script>
        // const button = document.getElementById("report")
        const buttontext = document.getElementsByClassName("buttontext")
        const reportlink = document.getElementById("reportlink")
        function butExp(){
            logo.src = "../Assets/logo.png";
            reportlink.classList.remove('rounded-circle');
            reportlink.classList.add('rounded-pill');
            buttontext[0].classList.remove('buttontext1');
        }
        function butCol(){
            reportlink.classList.add('rounded-circle');
            reportlink.classList.remove('rounded-pill');
            buttontext[0].classList.add('buttontext1');
        }
    </script>
</body>
</html>