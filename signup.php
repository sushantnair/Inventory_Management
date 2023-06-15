<?php 
include('config.php');

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $firstName = $_POST['signup-first-name'];
    $lastName = $_POST['signup-last-name'];
    $email = $_POST['signup-email'];
    $password = $_POST['signup-password'];
    $confirmPassword = $_POST['signup-confirm-password'];
    $role = $_POST['signup-role'];
    $idNumber = $_POST['signup-id-number'];

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email address";
        exit;
    }

    // Validate name
    if (empty($firstName) || empty($lastName)) {
        echo "First name and last name are required";
        exit;
    }

    // Check if password and confirm password match
    if ($password !== $confirmPassword) {
        echo "Password and confirm password do not match";
        exit;
    }

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and execute SQL query
    $sql = "INSERT INTO user_db (first_name, last_name, email, password, role, id_number)
            VALUES ('$firstName', '$lastName', '$email', '$password', '$role', '$idNumber')";

    if ($conn->query($sql) === TRUE) {
        echo "User registered successfully";
        header("Location:login_form.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close connection
    $conn->close();
}
?>
