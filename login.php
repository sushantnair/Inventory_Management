<?php 
include('connection.php');
session_start();
$email = mysqli_real_escape_string($conn,$_POST['email']);
$pass = mysqli_real_escape_string($conn,$_POST['pass']);

$sql = mysqli_query($conn," SELECT * FROM user WHERE email = '$email'");
$row = mysqli_fetch_array($sql);
echo password_verify($pass,$row['pass']);
if((is_array($row))&&(password_verify($pass,$row['password'])))
{
   $_SESSION['id']=$row['id'];
   $_SESSION["logged"]=true;
   header("Location:navbar.php");

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