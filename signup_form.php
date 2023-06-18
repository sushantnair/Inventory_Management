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
  <title>Signup Page</title>
  <link rel="stylesheet" href="CSS/signup.css">
</head>
<body>
<div class="container">
    <h2>Signup</h2>
    <form action="signup.php" method="POST">
      <label for="fname">First Name</label>
      <input type="text" id="fname" name="fname" required>

      <label for="lname">Last Name</label>
      <input type="text" id="lname" name="lname" required>

      <label for="email">Email</label>
      <input type="email" id="email" name="email" required pattern=".+@somaiya\.edu$"
             title="Please enter a valid @somaiya.edu email address">

      <label for="pass">Password</label>
      <input type="password" id="pass" name="pass" required>

      <label for="pass">Confirm Password</label>
      <input type="password" id="pass" name="cpass" required>

      <label for="role">Role</label>
      <select id="role" name="role" required>
        <option value="student">Student</option>
        <option value="lab-assistant">Lab Assistant</option>
        <option value="faculty">Faculty</option>
      </select>

      <label for="id">ID Number</label>
      <input type="text" id="id" name="id" required>

      <button type="submit">Signup</button>
    </form>
    <p>Already have an account? <a href="login_form.php"><button>Login</button></a></p>

  </div>
 
</body>
</html>