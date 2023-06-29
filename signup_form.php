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
  <title>Signup Page</title>
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script> -->
  <link rel="stylesheet" href="CSS/bootstrap.css">
  <script src="JS/bootstrap.bundle.js"></script>
  <link rel="stylesheet" href="CSS/signup.css">
</head>
<body>
<div class="container">
    <h2>Signup</h2>
    <form action="signup.php" method="POST">
      <div class="form-floating">
        <input class="form-control" type="text" id="name" name="name" placeholder="Full Name" required>
        <label for="name">Full Name</label>
      </div>

      <div class="form-floating">
      <input class="form-control" type="email" id="email" name="email" placeholder="Email" required pattern=".+@somaiya\.edu$"
             title="Please enter a valid @somaiya.edu email address">
      <label for="email">Email</label>
      </div>

      <div class="form-floating">
        <input class="form-control" type="password" id="pass" name="pass" placeholder="Password" required>
        <label for="pass">Password</label>
      </div>

      <div class="form-floating">
        <input class="form-control" type="password" id="pass" name="cpass" placeholder="Confirm Password" required>
        <label for="pass">Confirm Password</label>
      </div>

      <div class="form-floating">
      <select class="form-select mb-2" id="role" name="role" required>
        <option value="" disabled selected>Choose a role</option>
        <option value="student">Student</option>
        <option value="lab-assistant">Lab Assistant</option>
        <option value="faculty">Faculty</option>
      </select>
      <label for="role" class="select-label">Role</label>
      </div>

      <div class="form-floating">
      <select class="form-select" id="dept" name="dept" required>
        <option value="" disabled selected>Choose a Department</option>
        <option value="EXTC">EXTC</option>
        <option value="COMPS">COMPS</option>
      </select>
      <label for="dept" class="select-label">Department</label>
      </div>

      <div class="form-floating">
        <input class="form-control" type="text" id="id" name="id" placeholder="ID Number" required>
        <label for="id">ID Number</label>
      </div>

      <button type="submit">Signup</button>
    </form>
    <p>Already have an account? <a href="login_form.php"><button>Login</button></a></p>

  </div>
 
</body>
</html>