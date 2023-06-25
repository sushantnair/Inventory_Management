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