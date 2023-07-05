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
    }
    //If a user is logged in and is not an admin
    else if (isset($_SESSION['logged']) && $_SESSION['role']!='admin')
    {
		$role=$_SESSION['role'];
		if($role=='lab-assistant')
			header('Location:../LabAssistant/index.php');    
		else if($role=='student')
			header('Location:../Student/index.php');    
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
<body style="background-color: #f8f9fc;">
    <?php include('../Components/sidebar.php') ?>
    <div class="position-absolute container row w-100 top-0 ms-4" style="left: 100px; z-index:100;">
        <div class="h2 mt-4"><?php echo $id;?> - <u><?php echo $name;?></u></div>
        <div style="font-size:17px"><?php echo $dept;?></div>
        <!-- <hr class="mt-4 shadow mx-5"> -->
        <div class="col-xl-3 col-md-6 mt-4 mb-2" onclick="window.open('view_equ.php','_self')">
            <div class="card border-success border-5 border-end-0 border-top-0 border-bottom-0 rounded shadow-lg h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="card-head text-success text-uppercase mb-1">
                                Departments</div>
                            <div class="h4 card-content mb-0 text-dark">8</div>
                        </div>
                        <div class="col-auto me-2">
                            <i class="fa-solid fa-building-columns fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mt-4 mb-2">
            <div class="card border-success border-5 border-end-0 border-top-0 border-bottom-0 rounded shadow-lg h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="card-head text-success text-uppercase mb-1">
                                Total No. of Labs</div>
                            <div class="h4 card-content mb-0 text-dark">80</div>
                        </div>
                        <div class="col-auto me-2">
                            <i class="fa-solid fa-building fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mt-4 mb-2">
            <div class="card border-success border-5 border-end-0 border-top-0 border-bottom-0 rounded shadow-lg h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="card-head text-success text-uppercase mb-1">
                                No. of Active Labs</div>
                            <div class="h4 card-content mb-0 text-dark">68</div>
                        </div>
                        <div class="col-auto me-2">
                            <i class="fa-solid fa-building-circle-check fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr class="mt-4 shadow mx-4">
        <div class="col-xl-3 col-md-6 mt-4 mb-2">
            <div class="card border-primary border-5 border-end-0 border-top-0 border-bottom-0 rounded shadow-lg h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="card-head text-primary text-uppercase mb-1">
                                Lab Asst. Assigned</div>
                            <div class="h4 card-content mb-0 text-dark">8</div>
                        </div>
                        <div class="col-auto me-2">
                            <i class="fa-solid fa-person-circle-check fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mt-4 mb-2">
            <div class="card border-primary border-5 border-end-0 border-top-0 border-bottom-0 rounded shadow-lg h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="card-head text-primary text-uppercase mb-1">
                            Lab Asst. Not Assigned</div>
                            <div class="h4 card-content mb-0 text-dark">3</div>
                        </div>
                        <div class="col-auto me-2">
                            <i class="fa-solid fa-person-circle-question fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mt-4 mb-2">
            <div class="card border-primary border-5 border-end-0 border-top-0 border-bottom-0 rounded shadow-lg h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="card-head text-primary text-uppercase mb-1">
                            Lab Asst. Revoked</div>
                            <div class="h4 card-content mb-0 text-dark">4</div>
                        </div>
                        <div class="col-auto me-2">
                            <i class="fa-solid fa-person-circle-xmark fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mt-4 mb-2">
            <div class="card border-primary border-5 border-end-0 border-top-0 border-bottom-0 rounded shadow-lg h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="card-head text-primary text-uppercase mb-1">
                            Lab Asst. Requests</div>
                            <div class="h4 card-content mb-0 text-dark">4</div>
                        </div>
                        <div class="col-auto me-2">
                            <i class="fa-solid fa-person-circle-plus fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="position-absolute" id="report" style="bottom: 2rem; right: 3rem; z-index:1000;" onmouseenter="butExp()" onmouseleave="butCol()">
        <a href="#" id="reportlink" class="btn btn-danger rounded-circle shadow p-3">
        <span class="buttontext buttontext1" style="float:left; padding-right: 0.75em; font-weight: bold;">Request<br>Equipment</span>
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
