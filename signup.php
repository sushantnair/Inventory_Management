<?php 
include('connection.php');

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

    // Prepare and execute SQL query
    $sql1=mysqli_query($conn,"SELECT * FROM user where email='$email'");
    if(mysqli_num_rows($sql1)>0){
        echo "Email Id Already Exists"; 
        exit;
    }
    else 
    {
        $sql = "INSERT INTO user (fname, lname, email, password, role, id)
            VALUES ('$fname', '$lname', '$email', '$pass', '$role', '$id')";

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
