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


    //signup.php backend
    if(isset($_POST['name'])&&isset($_POST['email'])&&isset($_POST['pass'])&&isset($_POST['cpass'])&&isset($_POST['role'])&&isset($_POST['dept'])&&isset($_POST['id'])){
        $name = $_POST['name'];
        $email = $_POST['email'];
        $pass = $_POST['pass'];
        $cpass = $_POST['cpass'];
        $role = $_POST['role'];
        $dept = $_POST['dept'];
        $id = $_POST['id'];

        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Invalid email address";
            exit;
        }

        // Check if password and confirm password match
        if ($pass !== $cpass) 
        {
            header("Location:signup_form.php?pass=false");
            exit;
        }

        // Check if email already exists
        $sql_email_check = "SELECT * FROM user where email='$email'";
        $result_email_check = mysqli_query($conn, $sql_email_check);
        if(!$result_email_check){
            header("Location:signup_form.php?pass=false");
            exit;
        }

        // Check if id already exists
        $sql_id_check = "SELECT * FROM user where id='$id'";
        $result_id_check = mysqli_query($conn, $sql_id_check);
        if(!$result_id_check){
            header("Location:signup_form.php?conn=false");
            exit;
        }

        $row_count_email = mysqli_num_rows($result_email_check);
        $row_count_id = mysqli_num_rows($result_id_check);
        if($row_count_email > 0 || $row_count_id > 0)
        {
            header("Location:signup_form.php?error=true");
        }
        else 
        {
            //create a hashed password
            $passhash = password_hash($pass, PASSWORD_DEFAULT);
            
            //insert into table query
            $sql = "INSERT INTO user (name, email, password, role, dept, id) VALUES ('$name', '$email', '$passhash', '$role', '$dept', '$id')";
            $result = mysqli_query($conn, $sql);
            if(!$result){
                header("Location:signup_form.php?conn=false");
                exit;
            }
            header('Location: login_form.php');
        }
        // Close connection
        mysqli_close($conn);
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Signup Page</title>
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
                <div class="container ">
                    <h2>Welcome to</h2>
                    <h2 style="color: red; font-weight: bold;">KJSCE Lab Inventory Management</h2>
                    <hr>
                    <form action="signup_form.php" method="POST">
                        <!-- <div class="input-group mb-6"> -->
                            <!-- <div class="input-group-prepend"> -->
                        <div class="mx-auto">
                            <span class="icon position-absolute h4" style="z-index: 100; width:60px; height: 60px; text-align:center; vertical-align:middle;">👤 </span>
                            <!-- </div> -->
                            <div class="form-floating col-12 mb-6">
                                <input class="form-control" type="text" id="name" name="name" placeholder="Full Name" required>
                                <label class="label ms-2" for="name">Full Name</label>        
                            </div>
                        </div>
                        <!-- </div> -->
                        <br>
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
                        <div class="row">  
                            <!-- <div class="col-6">    
                            <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text py-3">🔑</span>
                            </div> -->
                            <div class="mx-auto col-6">
                                <span class="icon position-absolute h4" style="z-index: 100; width:60px; height: 60px; text-align:center; vertical-align:middle;">🔑 </span>
                                
                                <div class="form-floating col-12 mb-6">
                                    <input class="form-control" type="password" id="pass" name="pass" placeholder="Password" required>
                                    <label class="label ms-2" for="pass">Password</label>
                                </div>
                            </div>
                            <!-- </div> -->
                            <!-- <div class="col-6">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text py-3">🔒</span>
                                </div> -->
                            <div class="mx-auto col-6">
                            <span class="icon position-absolute h4" style="z-index: 100; width:60px; height: 60px; text-align:center; vertical-align:middle;">🔒 </span>
                            
                            <div class="form-floating col-12 mb-6">
                                <input class="form-control" type="password" id="pass" name="cpass" placeholder="Confirm Password" required>
                                <label class="label ms-2" for="pass">Confirm Password</label>
                            </div>
                        </div>
                        </div>
                        <br>
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
                        <br>

                        <!-- <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text py-3 px-3">@</span>
                            </div> -->
                            <div class="mx-auto">
                            <span class="icon position-absolute h4" style="z-index: 100; width:60px; height: 60px; text-align:center; vertical-align:middle;">🔑 </span>
                            
                            <div class="form-floating col-12 mb-6">
                            <input class="form-control" type="text" id="id" name="id" placeholder="ID Number" required>
                            <label class="label ms-2" for="id">ID Number</label>
                        </div>
                        </div>  
                        <br>

                        <button class="btn btn-danger col-lg-12 col-md-10 col-10 mb-4" type="submit" style="background-color: #D40000 ; color: white; height:45px;">Register</button>
                            </div>
                    </form>
                    <p style="text-align:center;">Already have an account? <a href="login_form.php">Login</a></p>

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
                This Email or ID number already exists.
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
                Password and Confirm Password do not match.
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
        if(isset($_GET['pass']))
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