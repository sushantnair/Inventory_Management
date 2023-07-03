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
	//Accept email & password from submitted form
	$email = mysqli_real_escape_string($conn,$_POST['email']);
	$pass = mysqli_real_escape_string($conn,$_POST['pass']);

	//check if email is registered
	$result_fetch_user_data = mysqli_query($conn, "SELECT * FROM user WHERE email = '$email'");
	if(!$result_fetch_user_data){
		header("Location:login_form.php?conn=false");
        exit;
	}
	$row_count = mysqli_num_rows($result_fetch_user_data);
	if($row_count == 0)
	{		
		header("Location:login_form.php?error=true");
	}
	$row = mysqli_fetch_array($result_fetch_user_data);

	//if email registered, check if password matches
	if((is_array($row))&&(password_verify($pass,$row['password'])))
	{
		$_SESSION['id']=$row['id'];
		$_SESSION['dept']=$row['dept'];
		$_SESSION['role']=$row['role'];
		$_SESSION['logged']=true;
		$_SESSION['status']=$row['status'];
		$role=$_SESSION['role'];
		
		//Redirect user to respective dashboards
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
			elseif($_SESSION['status']==0)
			{
				unset($_SESSION['logged']);
				header('Location:login_form.php');
			}
		}   
		else 
		{
			header("Location:login_form.php?conn=false");
        	exit;
		}
	}
	else
	{
		header("Location:login_form.php?error=false");
        exit;
	}

?>