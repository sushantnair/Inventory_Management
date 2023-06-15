<?php 
include('config.php');
session_start();
$email = mysqli_real_escape_string($conn,$_POST['login-email']);
$pass = mysqli_real_escape_string($conn,$_POST['login-password']);


if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
	$error = 'Invalid email format';
	echo "
	<html>
	<head></head>
	<body>
	<script>alert('$error');</script>
	</body>
	</html>";
} else{

$sql = " SELECT * FROM user_db WHERE email = '$email' && password = '$pass' ";
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) > 0){
   $query="SELECT id_number FROM user_db WHERE email = '$email' && password = '$pass'";
   $result1=mysqli_query($conn,$query);
   $row=mysqli_fetch_array($result1);
   $_SESSION['ID']=$row['ID'];
   header("Location:dashboard.php");
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
}
?>