<?php 
session_start();
include('connection.php');

$email = mysqli_real_escape_string($conn,$_POST['email']);
$pass = mysqli_real_escape_string($conn,$_POST['pass']);

$sql = mysqli_query($conn," SELECT * FROM user WHERE email = '$email'");
$row = mysqli_fetch_array($sql);
echo password_verify($pass,$row['pass']);
if((is_array($row))&&(password_verify($pass,$row['password'])))
{
   	$_SESSION['id']=$row['id'];
	$_SESSION['role']=$row['role'];
   	$_SESSION['logged']=true;
	$role=$_SESSION['role'];
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


}else{
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