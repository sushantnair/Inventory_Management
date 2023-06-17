<?php 
include('connection.php');
session_start();
$email = mysqli_real_escape_string($conn,$_POST['email']);
$pass = mysqli_real_escape_string($conn,$_POST['pass']);



$sql = " SELECT * FROM user WHERE email = '$email' && password = '$pass' ";
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) > 0){
   $query="SELECT id FROM user WHERE email = '$email' && password = '$pass'";
   $result1=mysqli_query($conn,$query);
   $row=mysqli_fetch_array($result1);
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