<?php
	session_start();
	if(isset($_SESSION['logged']))
	{
		include 'connection.php';
		$role=$_SESSION['role'];
		if($role=='admin')
			header('Location:Admin/dash_admin.php');    
		else if($role=='student')
			header('Location:Student/dash_student.php');    
		else if($role=='lab-assistant')
			header('Location:LabAssistant/dash_lab.php');   
	}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Login Page</title>
  <link rel="stylesheet" href="CSS/login.css">
</head>
<body>
  <div class="container">
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