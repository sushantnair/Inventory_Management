<?php 
	session_start();
	include('connection.php');
	//Checks if a user is logged in, if so, redirect
	if(isset($_SESSION['logged']))
	{
		$role=$_SESSION['role'];
		if($role=='admin')
			header('Location:Admin/dash.php');    
		else if($role=='student')
			header('Location:Student/dash.php');    
		else if($role=='lab-assistant')
		{
            if($_SESSION['status']==1)
            {
			    header('Location:LabAssistant/dash.php');
			}
			else if($_SESSION['status']==0)
            {
				unset($_SESSION['logged']);
				header('Location:login_form.php');
			}  
        }
	}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Login Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

        <link rel="stylesheet" href="CSS/signup.css">
</head>
<body>
	<div class="container-fluid">
        <div class="row">
            <div class="col-lg-5 col-md-6 col-12 ms-0 ps-0 pt-0" style="height:100vh;">
            <!-- <img src="Group 1.png" class="absolute img-fluid h-100"> -->
            <img src="Signup.png" class="position-absolute img-fluid ms-0 ps-0 h-100">
            </div>
			<div class="col-lg-6 col-md-6 col-12 mt-3">
                <div class="container">
                    <h2>Welcome to</h2>
                    <h2 style="color: red; font-weight: bold;">KJSCE lab Inventory Management</h2>
                    <hr>
					<button class="btn btn-secondary col-lg-12 col-md-10 col-12 mb-3 mt-4" style="background-color: #ffffff; border-width:2px; border-color: #f2f2f2; color: black; font-weight: bold; height:45px;"><span><img src="Glogo.png" style="object-fit: contain; max-height: 60%; margin-bottom: 3px;">&nbsp;&nbsp; Login with Google</span></button>
					<hr class="mb-3">
					
                    <form action="login.php" method="POST">
                        <!-- <div class="input-group mb-6"> -->
                            <!-- <div class="input-group-prepend"> -->
                            <!-- <div class="mx-auto">
                                <span class="icon position-absolute h4" style="z-index: 100; width:60px; height: 60px; text-align:center; vertical-align:middle;">👤 </span> -->
                            <!-- </div> -->
                            <!-- <div class="form-floating col-12 mb-6">
                                <input class="form-control" type="text" id="name" name="name" placeholder="Full Name" required>
                                <label class="label ms-2" for="name">Full Name</label>        
                            </div>
                            </div> -->
                        <!-- </div> -->
                        <!-- <br> -->
                        <!-- <div class="input-group mb-3">
                            <div class="input-group-prepend"> -->
                            <div class="mx-auto">
                            <span class="icon position-absolute h4" style="z-index: 100; width:60px; height: 60px; text-align:center; vertical-align:middle;">&#128231; </span>
                                <!-- <span class="input-group-text py-3">&#128231; </span> -->
                            <!-- </div> -->
                            <div class="form-floating col-12 mb-6">
                                <input class="form-control" type="email" id="email" name="email" placeholder="Email" required pattern=".+@somaiya\.edu$" title="Please enter a valid @somaiya.edu email address">             
                                <label class="label ms-2" for="email">Email</label>
                            </div>
                        	</div>    
                        <br> 
                        <!-- <div class="col-6">    
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text py-3">🔑</span>
                            </div> -->
                            <div class="mx-auto">
                            <span class="icon position-absolute h4" style="z-index: 100; width:60px; height: 60px; text-align:center; vertical-align:middle;">🔑 </span>
                            
                            <div class="form-floating col-12 mb-6">
                                <input class="form-control" type="password" id="pass" name="pass" placeholder="Password" required>
                                <label class="label ms-2" for="pass">Password</label>
                            </div>
                        </div>
						<br>
                        <!-- </div> -->
                        <!-- <div class="col-6">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text py-3">🔒</span>
                            </div> -->
                            <!-- <div class="mx-auto col-6">
                            <span class="icon position-absolute h4" style="z-index: 100; width:60px; height: 60px; text-align:center; vertical-align:middle;">🔒 </span>
                            
                            <div class="form-floating col-12 mb-6">
                                <input class="form-control" type="password" id="pass" name="cpass" placeholder="Confirm Password" required>
                                <label class="label ms-2" for="pass">Confirm Password</label>
                            </div>
                        </div>
                        </div> -->
                        <!-- <br>
                        <div class="row mb-6">
                        <div class="col-6 form-floating">
                            <select class="form-select" id="role" name="role" required>
                                <option value="" disabled selected>Choose a role</option>
                                <option value="student">Student</option>
                                <option value="lab-assistant">Lab Assistant</option>
                                <option value="faculty">Faculty</option>
                            </select>
                            <label for="role" class="select-label ms-3">Role</label>
                        </div>

                        <div class="col-6 form-floating">
                        <select class="form-select" id="dept" name="dept" required>
                            <option value="" disabled selected>Choose a Department</option>
                            <?php 
                                $fetch_departments=mysqli_query($conn,"SELECT * FROM departments");
                                while($dept_row=mysqli_fetch_array($fetch_departments,MYSQLI_ASSOC))
                                {
                                    
                                    ?>
                                    <option value=<?php echo $dept_row['dept']; ?>><?php echo $dept_row['dept']; ?></option>
                                    <?php
                                }
                            ?>

                        </select>
                        <label for="dept" class="select-label ms-3">Department</label>
                        </div>
                        </div>
                        <br> -->

                        <!-- <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text py-3 px-3">@</span>
                            </div> -->
                            <!-- <div class="mx-auto">
                            <span class="icon position-absolute h4" style="z-index: 100; width:60px; height: 60px; text-align:center; vertical-align:middle;">🔑 </span>
                            
                            <div class="form-floating col-12 mb-6">
                            <input class="form-control" type="text" id="id" name="id" placeholder="ID Number" required>
                            <label class="label ms-2" for="id">ID Number</label>
                        </div>
                        </div>  
                        <br> -->

                        <button class="btn btn-danger col-lg-12 col-md-10 col-10 mb-4" type="submit" style="background-color: #D40000 ; color: white; height:45px;">Login</button>
                            </div>
                    </form>
                    <p style="text-align:center;">Don't have an account? <a href="signup_form.php">Signup</a></p>

                </div>
            </div>
        </div>
    </div>
    <h1><u>KJSCE Inventory Management</u></h1>
    <h2>Login</h2>
    <form action="login.php" method="POST">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" required>

      <label for="pass">Password</label>
      <input type="password" id="pass" name="pass" required>

      <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="signup_form.php"><button>Signup</button></a></p>
  
  </div>
</body>
</html>