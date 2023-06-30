<?php 
    include('connection.php');
	session_start();
    //Checks if a user is logged in, if so, redirect
	if(isset($_SESSION['logged']))
	{
		$role=$_SESSION['role'];
		if($role=='admin')
			header('Location:Admin/dash.php');    
		else if($role=='student')
			header('Location:Student/dash.php');    
		else if($role=='lab-assistant')
			header('Location:LabAssistant/dash.php');   
	}
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

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if email already exists
    $sql_email_check = "SELECT * 
                        FROM user 
                        where email='$email'";
    $result_email_check = mysqli_query($conn, $sql_email_check);
    if(!$result_email_check){
        $response = array("success" => false, "message" => "There was a problem in the connection.");
        echo json_encode($response);
        return;
    }

    // Check if id already exists
    $sql_id_check = "SELECT * 
                     FROM user 
                     where id='$id'";
    $result_id_check = mysqli_query($conn, $sql_id_check);
    if(!$result_id_check){
        $response = array("success" => false, "message" => "There was a problem in the connection.");
        echo json_encode($response);
        return;
    }

    $row_count_email = mysqli_num_rows($result_email_check);
    $row_count_id = mysqli_num_rows($result_id_check);
    if($row_count_email > 0 || $row_count_id > 0)
    {
        $response = array("success" => false, "message" => "This account is already registered. Please login.");
        echo json_encode($response);
        return;
    }
    else 
    {
        //create a hashed password
        $passhash = password_hash($pass, PASSWORD_DEFAULT);
        
        //insert into table query
        $sql = "INSERT INTO user (name, email, password, role, dept, id)
                VALUES ('$name', '$email', '$passhash', '$role', '$dept', '$id')";
        $result = mysqli_query($conn, $sql);
        if(!$result){
            $response = array("success" => false, "message" => "There was a problem while inserting data into the database.");
            echo json_encode($response);
            return;
        }
        // $response = array("success" => true, "message" => "Account created successfully.");
        // echo json_encode($response);
        header('Location: login_form.php');
    }
    // Close connection
    mysqli_close($conn);
?>
