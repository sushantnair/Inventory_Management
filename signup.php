<?php 
    include('connection.php');
	session_start();
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

    // Check if form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve form data
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $email = $_POST['email'];
        $pass = $_POST['pass'];
        $cpass = $_POST['cpass'];
        $role = $_POST['role'];
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
            $sql = "INSERT INTO user (fname, lname, email, password, role, id)
                VALUES ('$fname', '$lname', '$email', '$passhash', '$role', '$id')";

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
?>
