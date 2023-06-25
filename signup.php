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

    // Check if form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve form data
        $name = $_POST['name'];
        $email = $_POST['email'];
        $pass = $_POST['pass'];
        $cpass = $_POST['cpass'];
        $role = $_POST['role'];
        $dept = $_POST['dept'];
        $id = $_POST['id'];

        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Invalid email address";
            exit;
        }

        // Check if password and confirm password match
        if ($pass !== $cpass) {
            echo "Password and confirm password do not match";
            exit;
        }
        else
        {
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Check if email already exists
            $sql1=mysqli_query($conn,"SELECT * FROM user where email='$email'");
            // Check if id already exists
            $sql2=mysqli_query($conn,"SELECT * FROM user where id='$id'");
            if(mysqli_num_rows($sql1)>0 || mysqli_num_rows($sql2)>0)
            {
                echo "Email Id or ID Number Already Exists"; 
                exit;
            }
            else 
            {
                //create a hashed password
                $passhash = password_hash($pass, PASSWORD_DEFAULT);
                
                //insert into table query
                $sql = "INSERT INTO user (name, email, password, role, dept, id)
                    VALUES ('$name', '$email', '$passhash', '$role', '$dept', '$id')";

                if ($conn->query($sql) === TRUE) {
                    echo "User registered successfully";
                    header("Location:login_form.php");
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            }
            // Close connection
            $conn->close();

        }
        
    }
?>
