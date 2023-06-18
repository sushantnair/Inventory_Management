<?php 
	session_start();
	include('connection.php');
	//Checks if a user is logged in, if so, redirect
	if(isset($_SESSION['logged']))
	{
		$role=$_SESSION['role'];
		if($role=='admin')
			header('Location:Admin/dash_admin.php');    
		else if($role=='student')
			header('Location:Student/dash_student.php');    
		else if($role=='lab-assistant')
			header('Location:LabAssistant/dash_lab.php');   
	}
	//Accept email & password from submitted form
	$email = mysqli_real_escape_string($conn,$_POST['email']);
	$pass = mysqli_real_escape_string($conn,$_POST['pass']);

	//check if email is registered
	$sql = mysqli_query($conn," SELECT * FROM user WHERE email = '$email'");
	$row = mysqli_fetch_array($sql);

	//if email registered, check if password matches
	if((is_array($row))&&(password_verify($pass,$row['password'])))
	{
		$_SESSION['id']=$row['id'];
		$_SESSION['role']=$row['role'];
		$_SESSION['logged']=true;
		$role=$_SESSION['role'];
		
		//Redirect user to respective dashboards
		if($role=='admin')
			header('Location:Admin/dash_admin.php');    
		else if($role=='student')
			header('Location:Student/dash_student.php');    
		else if($role=='lab-assistant')
			header('Location:LabAssistant/dash_lab.php');   
		else {
			$error = 'Role Undefined';
			echo "
			<html>
			<head></head>
			<body>
			<script>alert('$error');</script>
			</body>
			</html>";
			header('Location:Login_form.php');
		}
	}
	// Wrong Input
	else
	{
		$error = 'WRONG ENTRY';
		echo "
		<html>
		<head></head>
		<body>
		<script>alert('$error');</script>
		</body>
		</html>";
	}

?>