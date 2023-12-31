<?php
	session_start();
	include('connection.php');

    //Include required PHPMailer files
	require './OTP/includes/PHPMailer.php';
	require './OTP/includes/SMTP.php';
	require './OTP/includes/Exception.php';
//Define name spaces
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;

	//Checks if a user is logged in, if so, redirect
	if(isset($_SESSION['logged']))
	{
		$role=$_SESSION['role'];
		if($role=='admin')
			header('Location:./Admin/index.php');    
		else if($role=='user')
			header('Location:./User/index.php');    
		else if($role=='lab-assistant')
		{
            if($_SESSION['status']==1)
            {
			    header('Location:./LabAssistant/index.php');
			}
			else if($_SESSION['status']==0)
            {
				unset($_SESSION['logged']);
				header('Location:login.php');
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
            header("Location:signup.php?pass=false");
            exit;
        }

        // Check if email already exists
        $sql_email_check = "SELECT * FROM user where email='$email'";
        $result_email_check = mysqli_query($conn, $sql_email_check);
        if(!$result_email_check){
            header("Location:signup.php?pass=false");
            exit;
        }

        // Check if id already exists
        $sql_id_check = "SELECT * FROM user where id='$id'";
        $result_id_check = mysqli_query($conn, $sql_id_check);
        if(!$result_id_check){
            header("Location:signup.php?conn=false");
            exit;
        }

        $row_count_email = mysqli_num_rows($result_email_check);
        $row_count_id = mysqli_num_rows($result_id_check);
        if($row_count_email > 0 || $row_count_id > 0)
        {
            header("Location:signup.php?error=true");
        }
        else 
        {
            //create a hashed password
            $passhash = password_hash($pass, PASSWORD_DEFAULT);
            $vcode = rand(1000,9999);            
            //insert into table query
            $sql = "INSERT INTO user (name, email, password, role, dept, id,vcode) VALUES ('$name', '$email', '$passhash', '$role', '$dept', '$id','$vcode')";
            $result = mysqli_query($conn, $sql);
            if(!$result){
                header("Location:signup.php?conn=false");
                exit;
            }

/*##########Script Information#########
  # Purpose: Send mail Using PHPMailer#
  #          & Gmail SMTP Server 	  #
  # Created: 24-11-2019 			  #
  #	Author : Hafiz Haider			  #
  # Version: 1.0					  #
  # Website: www.BroExperts.com 	  #
  #####################################*/

//Create instance of PHPMailer
	$mail = new PHPMailer();
//Set mailer to use smtp
	$mail->isSMTP();
//Define smtp host
	$mail->Host = "smtp.gmail.com";
//Enable smtp authentication
	$mail->SMTPAuth = true;
//Set smtp encryption type (ssl/tls)
	$mail->SMTPSecure = "tls";
//Port to connect smtp
	$mail->Port = "587";
//Set gmail username
	$mail->Username = "imskjsce@gmail.com";
//Set gmail password
	$mail->Password = $otppass;
//Email subject
	$mail->Subject = "OTP Verification";
//Set sender email
	$mail->setFrom('imskjsce@gmail.com');
//Enable HTML
	$mail->isHTML(true);
//Attachment
	// $mail->addAttachment('img/attachment.png');
//Email body
	$mail->Body = "<h1>Your verification code is ".$vcode;
//Add recipient
	$mail->addAddress($email);
//Finally send email
    $mail->send(); 
//Closing smtp connection
	$mail->smtpClose();

            header('Location: ./OTP/index.php');

        }
        // Close connection
        mysqli_close($conn);
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>IM-KJSCE</title>
        <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"> -->
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script> --> 
        
        <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="CSS/signup.css">
        <script>
            // var dropvalue = "";
            // function getVal() {
            //     const dropdown = document.getElementByID('role');
            //     dropvalue = dropdown.value;
            //     const register = document.getElementByClassNames('')
            // }
            function getVal() {
                if($('#role').val()=='lab-assistant'){
                    $('#myModal').modal('show');
                } else {
                    $('#registerButton').removeAttr("type").attr("type", "submit");
                }
            }
        </script>
    </head>
<body>
    
    <div class="container-fluid">
        <div class="row">
        <?php include('Components/section.php') ?>

            <div class="col-lg-6 col-md-6 col-12 mt-3">
                <div class="container ">
                    <h2>Welcome to</h2>
                    <h2 style="color: red; font-weight: bold;">KJSCE Lab Inventory Management</h2>
                    <hr>
                    <form action="signup.php" method="POST">
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
                                    <option value="user">User</option>
                                    <option value="lab-assistant">Lab Assistant</option>
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
                                            <option value="<?php echo $dept_row['dept']; ?>"><?php echo $dept_row['dept']; ?></option>
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

                        <!-- <button class="btn btn-danger col-lg-12 col-md-10 col-10 mb-4" type="submit" style="background-color: #D40000 ; color: white; height:45px;">Register</button> -->
                        <button class="btn btn-danger col-lg-12 col-md-10 col-10 mb-4" type="button" id="registerButton" style="background-color: #D40000; color: white; height:45px;" onclick="getVal()">Register</button>
                        </div>
                        <!-- Modal -->
                        <div class="modal fade" id="myModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title text-danger" id="staticBackdropLabel">Register as a Lab Assistant</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body" style="text-align: center;">
                                                <?php
                                                
                                                    echo "<p class='text-danger' style='margin:0;'>Are you sure you want to register as a Lab Assistant? If these credentials are not associated with a lab assistant account, action could be taken!</p>";

                                                    echo "<p style='font-size: small; margin:0;'>This action cannot be reversed.</p>";
                                                    echo "<p style='font-size: small; margin:0;'>Click 'Cancel' to edit your details.</p>";

                                                        
                                                ?>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn alert-danger" data-bs-dismiss="modal">No, Cancel</button>
                                                <button class="btn btn-outline-danger" type="submit" name="delete">Yes, Register</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                    </form>
                    <p style="text-align:center;">Already have an account? <a href="login.php">Login</a></p>

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