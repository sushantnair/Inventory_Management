<?php 
	session_start();
	include('connection.php');
	//Checks if a user is logged in, if so, redirect
	if(isset($_SESSION['logged']))
	{
		$role=$_SESSION['role'];
		if($role=='admin')
			header('Location:Admin/index.php');    
		else if($role=='User')
			header('Location:User/index.php');    
		else if($role=='lab-assistant')
		{
            if($_SESSION['status']==1)
            {
			    header('Location:LabAssistant/index.php');
			}
			else if($_SESSION['status']==0)
            {
				unset($_SESSION['logged']);
				header('Location:login.php');
			}  
        }
	}

//login.php backend
    if(isset($_POST['email'])&&isset($_POST['pass'])){
        //Accept email & password from submitted form
        $email = mysqli_real_escape_string($conn,$_POST['email']);
	    $pass = mysqli_real_escape_string($conn,$_POST['pass']);
        //check if email is registered
        $result_fetch_user_data = mysqli_query($conn, "SELECT * FROM user WHERE email = '$email'");
        if(!$result_fetch_user_data){
            header("Location:login.php?conn=false");
            exit;
        }
        $row_count = mysqli_num_rows($result_fetch_user_data);
        if($row_count == 0)
        {		
            header("Location:login.php?error=true");
        }
        $row = mysqli_fetch_array($result_fetch_user_data);

        //if email registered, check if password matches
        if((is_array($row))&&(password_verify($pass,$row['password'])))
        {
            $_SESSION['id']=$row['id'];
            $_SESSION['dept']=$row['dept'];
            $_SESSION['role']=$row['role'];
            $_SESSION['logged']=true;
            $_SESSION['status']=$row['status'];
            $_SESSION['vs']=$row['vstatus'];
            $role=$_SESSION['role'];
            
            //Redirect user to respective dashboards
        if($_SESSION['vs']==1){
            if($role=='admin')
                header('Location:Admin/index.php');    
            else if($role=='User')
                header('Location:User/index.php');    
            else if($role=='lab-assistant')
            {
                if($_SESSION['status']==1)
                {
                    header('Location:LabAssistant/index.php');
                }
                elseif($_SESSION['status']==0)
                {
                    unset($_SESSION['logged']);
                    header('Location:login.php');
                }
            }   
            else 
            {
                header("Location:login.php?conn=false");
                exit;
            }
        }else{
            unset($_SESSION['logged']);
            header('Location:login.php');
        }
        }
        else{
        	header("Location:login.php?error=false");
            exit;
        }
    }

?>


<!DOCTYPE html>
<html>
<head>
    <title>Login Page</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"> -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script> --> 
    
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="CSS/signup.css">
</head>
<body>
	<div class="container-fluid">
        <div class="row">
            <div class="col-lg-5 col-md-6 col-12 ms-0 ps-0 pt-0" style="height:100vh;">
            <img src="Assets/Signup.png" class="position-absolute img-fluid ms-0 ps-0 h-100">
            </div>
			<div class="col-lg-6 col-md-6 col-12 mt-3">
                <div class="container">
                    <h2>Welcome to</h2>
                    <h2 style="color: red; font-weight: bold;">KJSCE Lab Inventory Management</h2>
                    <hr>
					<button class="btn btn-secondary col-lg-12 col-md-10 col-12 mb-3 mt-4" style="background-color: #ffffff; border-width:2px; border-color: #f2f2f2; color: black; font-weight: bold; height:45px;"><span><img src="Assets/Glogo.png" style="object-fit: contain; max-height: 60%; margin-bottom: 3px;">&nbsp;&nbsp; Login with Google</span></button>
					<hr class="mb-3">
					
                    <form action="login.php" method="POST">  
                        <div class="mx-auto">
                            <span class="icon position-absolute h4" style="z-index: 100; width:60px; height: 60px; text-align:center; vertical-align:middle;">&#128231; </span>
                            
                            <div class="form-floating col-12 mb-6">
                                <input class="form-control" type="email" id="email" name="email" placeholder="Email" required pattern=".+@somaiya\.edu$" title="Please enter a valid @somaiya.edu email address">             
                                <label class="label ms-2" for="email">Email</label>
                            </div>
                        </div>    
                        <br> 
                            <div class="mx-auto">
                                <span class="icon position-absolute h4" style="z-index: 100; width:60px; height: 60px; text-align:center; vertical-align:middle;">🔑 </span>
                                
                                <div class="form-floating col-12 mb-6">
                                    <input class="form-control" type="password" id="pass" name="pass" placeholder="Password" required>
                                    <label class="label ms-2" for="pass">Password</label>
                            </div>
                        </div>
						<br>
                       
                        <button class="btn btn-danger col-lg-12 col-md-10 col-10 mb-4" type="submit" style="background-color: #D40000 ; color: white; height:45px;">Login</button>
                            </div>
                    </form>
                    <p style="text-align:center;">Don't have an account? <a href="OTP/signup.php">Signup</a></p>

                </div>
            </div>
        </div>
    </div>
    <div class="toast-container position-fixed top-0 end-0 p-3 ">
        <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto text-danger">Error</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body alert-danger">
                Invalid Login Credentials
            </div>
        </div>
    </div>
    <div class="toast-container position-fixed top-0 end-0 p-3 ">
        <div id="liveToast2" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto text-danger">Error</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body alert-danger">
                Error in connecting to the database.
            </div>
        </div>
    </div>
    <?php
        if(isset($_GET['error']))
        {
            
            ?>
                <script>
                    const toastLiveExample = document.getElementById('liveToast')
                    const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastLiveExample)
                    toastBootstrap.show()
                </script> 
            <?php 
        }
        if(isset($_GET['conn']))
        {
            
            ?>
                <script>
                    const toastLiveExample2 = document.getElementById('liveToast2')
                    const toastBootstrap2 = bootstrap.Toast.getOrCreateInstance(toastLiveExample2)
                    toastBootstrap2.show()
                </script> 
            <?php 
        }
        

    ?>
</body>
</html>