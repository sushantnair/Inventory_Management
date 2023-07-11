<?php 
   session_start();
    //If a user is logged in and is an admin
    if (isset($_SESSION['logged']) && $_SESSION['role']=='admin') 
    {
        include '../connection.php';
        $id=$_SESSION['id'];
        $fetch_name=mysqli_query($conn,"SELECT * FROM user WHERE id=$id");
        $row=mysqli_fetch_array($fetch_name,MYSQLI_ASSOC);
        $name=$row['name'];
        $dept=$row['dept'];
        $fetch_departments=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(dept) AS sum FROM departments"));
        $num_dept=$fetch_departments['sum'];
        if($dept=='')
        {
            $fetch_registered_assist=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(id) AS sum FROM user WHERE role='lab-assistant'"));
            $reg_assist=$fetch_registered_assist['sum'];

            $fetch_labs=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(labno) AS sum FROM labs"));
            $labs=$fetch_labs['sum'];

            $fetch_active_labs=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(labno) AS sum FROM labs WHERE active='yes'"));
            $a_labs=$fetch_active_labs['sum'];
            
            $fetch_assign_assist=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(labno) AS sum FROM labs WHERE assistid!=0"));
            $a_assist=$fetch_assign_assist['sum'];
            
            $fetch_assist=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(id) AS sum FROM user WHERE role='lab-assistant' AND status=1"));
            $num_assist=$fetch_assist['sum'];
            $na_assist=$num_assist-$a_assist;

            $fetch_revoked_assist=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(id) AS sum FROM user WHERE role='lab-assistant' AND status=-1"));
            $r_assist=$fetch_revoked_assist['sum'];

            //TO BE EDITED FOR PENDING AND REVOKED ASSISTANTS
            $fetch_pending_assist=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(id) AS sum FROM user WHERE role='lab-assistant' AND status=0"));
            $p_assist=$fetch_pending_assist['sum'];
        }
        else
        {
            $fetch_registered_assist=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(id) AS sum FROM user WHERE role='lab-assistant' AND dept='$dept'"));
            $reg_assist=$fetch_registered_assist['sum'];

            $fetch_labs=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(labno) AS sum FROM labs WHERE dept='$dept'"));
            $labs=$fetch_labs['sum'];

            $fetch_active_labs=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(labno) AS sum FROM labs WHERE active='yes' AND dept='$dept'"));
            $a_labs=$fetch_active_labs['sum'];
            
            $fetch_assign_assist=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(labno) AS sum FROM labs WHERE assistid!=0 AND dept='$dept'"));
            $a_assist=$fetch_assign_assist['sum'];
            
            $fetch_assist=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(id) AS sum FROM user WHERE role='lab-assistant' AND status=1 AND dept='$dept'"));
            $num_assist=$fetch_assist['sum'];
            $na_assist=$num_assist-$a_assist;

            $fetch_revoked_assist=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(id) AS sum FROM user WHERE role='lab-assistant' AND status=0 AND dept='$dept'"));
            $r_assist=$fetch_revoked_assist['sum'];

            //TO BE EDITED FOR PENDING AND REVOKED ASSISTANTS
            $fetch_pending_assist=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(id) AS sum FROM user WHERE role='lab-assistant' AND status=0 AND dept='$dept'"));
            $p_assist=$fetch_pending_assist['sum'];
        }

    }
    //If a user is logged in and is not an admin
    else if (isset($_SESSION['logged']) && $_SESSION['role']!='admin')
    {
		$role=$_SESSION['role'];
		if($role=='lab-assistant')
			header('Location:../LabAssistant/index.php');    
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
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
    <link rel="stylesheet" type="text/css" href="../CSS/bootstrap.min.css">
    <!-- ../ is used to go one level up from Admin folder. -->
    <title>IM-KJSCE | Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" /><!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">-->
    <!-- <link rel="stylesheet" href="../CSS/bootstrap.min.css"> -->
    <!-- using an offline copy saves time spent for loading bootstrap from online source  -->
    <link rel="stylesheet" href="CSS/styles.css">
</head>
<body style="background-color: #f8f9fc;overflow-x: hidden;">
    <?php include('../Components/sidebar.php') ?>
    <div class="position-absolute container row w-100 top-0 ms-4" style="left: 100px; z-index:100;">
        <div class="h2 mt-4"><?php echo $id;?> - <u><?php echo $name;?></u></div>
        <!-- <div style="font-size:17px"><?php if($dept!='') echo $dept;?></div> -->
        <div style="font-size:17px"><?php if($dept!='' && $role='admin') echo $dept; else echo "Master Admin"?></div>

        <!-- <hr class="mt-4 shadow mx-5"> -->
        <div class="col-lg-4 col-md-6 mt-4 mb-2">
            <div class="card border-success border-5 border-end-0 border-top-0 border-bottom-0 rounded shadow-lg h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="card-head text-success text-uppercase mb-1">
                                Departments</div>
                                <div class="h4 card-content mb-0 text-dark"><?php if($num_dept>0) echo $num_dept; else echo 0; ?></div>
                        </div>
                        <div class="col-auto me-2">
                            <i class="fa-solid fa-building-columns fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mt-4 mb-2" onclick="window.open('lab.php','_self')">
            <div class="card border-success border-5 border-end-0 border-top-0 border-bottom-0 rounded shadow-lg h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="card-head text-success text-uppercase mb-1">
                                Total No. of Labs</div>
                                <div class="h4 card-content mb-0 text-dark"><?php if($labs>0) echo $labs; else echo 0; ?></div>
                        </div>
                        <div class="col-auto me-2">
                            <i class="fa-solid fa-building fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mt-4 mb-2" onclick="window.open('lab.php','_self')">
            <div class="card border-success border-5 border-end-0 border-top-0 border-bottom-0 rounded shadow-lg h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="card-head text-success text-uppercase mb-1">
                                No. of Active Labs</div>
                                <div class="h4 card-content mb-0 text-dark"><?php if($a_labs>0) echo $a_labs; else echo 0; ?></div>
                        </div>
                        <div class="col-auto me-2">
                            <i class="fa-solid fa-building-circle-check fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr class="mt-4 shadow mx-5" style="width:87%">

        <div class="col-xl-3 col-md-6 mt-4 mb-2" onclick="window.open('assist.php','_self')">
            <div class="card border-primary border-5 border-end-0 border-top-0 border-bottom-0 rounded shadow-lg h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="card-head text-primary text-uppercase mb-1">
                                Lab Asst. Registered</div>
                            <div class="h4 card-content mb-0 text-dark"><?php if($reg_assist>0) echo $reg_assist; else echo 0; ?></div>
                        </div>
                        <div class="col-auto me-2">
                            <i class="fa-solid fa-users fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mt-4 mb-2" onclick="window.open('assist.php','_self')">
            <div class="card border-primary border-5 border-end-0 border-top-0 border-bottom-0 rounded shadow-lg h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="card-head text-primary text-uppercase mb-1">
                                Lab Asst. Assigned</div>
                            <div class="h4 card-content mb-0 text-dark"><?php if($a_assist>0) echo $a_assist; else echo 0; ?></div>
                        </div>
                        <div class="col-auto me-2">
                            <i class="fa-solid fa-user-check fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mt-4 mb-2" onclick="window.open('assist.php','_self')">
            <div class="card border-primary border-5 border-end-0 border-top-0 border-bottom-0 rounded shadow-lg h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="card-head text-primary text-uppercase mb-1">
                            Lab Asst. Not Assigned</div>
                            <div class="h4 card-content mb-0 text-dark"><?php if($na_assist>0) echo $na_assist; else echo 0; ?></div>
                        </div>
                        <div class="col-auto me-2">
                            <i class="fa-solid fa-user-gear fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mt-4 mb-2" onclick="window.open('assist.php','_self')">
            <div class="card border-primary border-5 border-end-0 border-top-0 border-bottom-0 rounded shadow-lg h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="card-head text-primary text-uppercase mb-1">
                            Lab Asst. Revoked</div>
                            <div class="h4 card-content mb-0 text-dark"><?php if($r_assist>0) echo $r_assist; else echo 0; ?></div>
                        </div>
                        <div class="col-auto me-2">
                            <i class="fa-solid fa-user-xmark fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mt-4 mb-2" onclick="window.open('assist.php','_self')">
            <div class="card border-primary border-5 border-end-0 border-top-0 border-bottom-0 rounded shadow-lg h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="card-head text-primary text-uppercase mb-1">
                            Lab Asst. Requests</div>
                            <div class="h4 card-content mb-0 text-dark"><?php if($p_assist>0) {echo $p_assist;} else echo 0; ?></div>
                        </div>
                        <div class="col-auto me-2">
                            <i class="fa-solid fa-user-plus fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="position-absolute" id="report" style="bottom: 2rem; right: 3rem; z-index:1000;" onmouseenter="butExp()" onmouseleave="butCol()">
        <a href="#" id="reportlink" class="btn btn-danger rounded-circle shadow p-3">
        <span class="buttontext buttontext1" style="float:left; padding-right: 0.75em; font-weight: bold;">Generate<br>Report</span>
        <span style="float:right;">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-file-earmark-bar-graph" viewBox="0 0 16 16">
        <path d="M10 13.5a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-6a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v6zm-2.5.5a.5.5 0 0 1-.5-.5v-4a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-.5.5h-1zm-3 0a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5h-1z"/>
        <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
        </svg>
        </span>
    </div>

</body>
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
</html>
