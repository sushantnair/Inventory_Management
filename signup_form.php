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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  <!-- <link rel="stylesheet" href="CSS/bootstrap.css">
  <script src="JS/bootstrap.bundle.js"></script>
  <link rel="stylesheet" href="CSS/signup.css"> -->
  <style>
    .icon {
    position: absolute;
    left: 15px;
    top: 0;
    font-size: 25px;
    color: #4b00ff;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}
    #para{
      background-color: ;
      /* height: 100%;
      width: 50%; */
      
    }
    /* #para2{
      
      height: 100%;
      width: 50%;
    } */
  </style>
</head>
<body>
<div class="container-fluid">
  <div class="row">
    <div class="col-lg-6 col-md-6 col-12">
<img src="Signup .png" class="img-fluid ">
    </div>
    <div class="col-lg-6 col-md-6 col-12">
    <div class="container ">
    <h2>Welcome to</h2>
    <h2 style="color: red; font-weight: bold;">KJSCE lab Inventory Management</h2>
    <hr>
    <form action="signup.php" method="POST">
    <div class="input-group mb-6">
    <div class="input-group-prepend">
    <span class="input-group-text">👤 </span>
  </div>
      <div class="form-floating col-lg-10 col-md-10 col-10">
        <input class="form-control" type="text" id="name" name="name" placeholder="Full Name" required>
        <label for="name">Full Name</label>        
      
  </div>
  </div>
  <br>
      <div class="input-group mb-3">
      <div class="input-group-prepend">
      <span class="input-group-text">&#128231; </span>
  </div>
      <div class="form-floating col-lg-10 col-md-10 col-10">
      
      <input class="form-control" type="email" id="email" name="email" placeholder="Email" required pattern=".+@somaiya\.edu$"
             title="Please enter a valid @somaiya.edu email address">             
      <label for="email">Email</label>
  </div>
  </div>    
      <br>      
      <div class="input-group mb-3">
      <div class="input-group-prepend">
      <span class="input-group-text">🔑</span>
  </div>
      <div class="form-floating col-lg-3 col-md-3 col-6">
        <input class="form-control " type="password" id="pass" name="pass" placeholder="Password" required>
        <label for="pass">Password</label>
        </div>
      
      </div>
      <div class="input-group mb-3">
      <div class="input-group-prepend">
      <span class="input-group-text">🔒</span>
  </div>
      <div class="form-floating col-lg-3 col-md-3 col-6">
        <input class="form-control" type="password" id="pass" name="cpass" placeholder="Confirm Password" required>
        <label for="pass">Confirm Password</label>
        </div>
      
      </div><br>

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
        <?php 
          $fetch_departments=mysqli_query($conn,"SELECT * FROM departments");
          while($dept_row=mysqli_fetch_array($fetch_departments,MYSQLI_ASSOC))
          {
              
              ?>
              <option value=<?php echo $dept_row['dept']; ?>><?php echo $dept_row['dept']; ?></option>
              <?php
          }
        ?>
        <!-- <option value="EXTC">EXTC</option>
        <option value="COMPS">COMPS</option> -->
      </select>
      <label for="dept" class="select-label">Department</label>
      </div><br>

      <div class="form-floating">
        <input class="form-control" type="text" id="id" name="id" placeholder="ID Number" required>
        <label for="id">ID Number</label>
      </div>
      <br>

      <button class="col-lg-12 col-md-10 col-10" type="submit" style="background-color: red ; color: white;">Register</button>
    </form>
    <p>Already have an account? <a href="login_form.php">Login</a></p>

  </div>
  <div class="input-group mb-3">
  <div class="input-group-prepend">
    <span class="input-group-text" id="basic-addon1">@</span>
  </div>
  <div class="form-floating">
        <input class="form-control" type="text" id="name" name="name" placeholder="Full Name" required>
        <label for="name">Full Name</label>  
        </div>
</div>
  
</body>
</html>