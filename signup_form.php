<!DOCTYPE html>
<html>
<head>
  <title>Signup Page</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      text-align: center;
    }

    .container {
      margin-top: 200px;
      width: 300px;
      padding: 20px;
      background-color: #fff;
      border-radius: 5px;
      margin: 0 auto;
    }

    input[type=text],
    input[type=email],
    input[type=password],
    select {
      width: 100%;
      padding: 12px 20px;
      margin: 8px 0;
      display: inline-block;
      border: 1px solid #ccc;
      box-sizing: border-box;
    }

    button {
      background-color: #4CAF50;
      color: white;
      padding: 14px 20px;
      margin: 8px 0;
      border: none;
      cursor: pointer;
      width: 100%;
    }

    button:hover {
      opacity: 0.8;
    }
  </style>
</head>
<body>
<div class="container">
    <h2>Signup</h2>
    <form action="signup.php" method="POST">
      <label for="signup-first-name">First Name</label>
      <input type="text" id="signup-first-name" name="signup-first-name" required>

      <label for="signup-last-name">Last Name</label>
      <input type="text" id="signup-last-name" name="signup-last-name" required>

      <label for="signup-email">Email</label>
      <input type="email" id="signup-email" name="signup-email" required pattern=".+@somaiya\.edu$"
             title="Please enter a valid @somaiya.edu email address">

      <label for="signup-password">Password</label>
      <input type="password" id="signup-password" name="signup-password" required>

      <label for="signup-password">Confirm Password</label>
      <input type="password" id="signup-password" name="signup-confirm-password" required>

      <label for="signup-role">Role</label>
      <select id="signup-role" name="signup-role" required>
        <option value="student">Student</option>
        <option value="lab-assistant">Lab Assistant</option>
        <option value="faculty">Faculty</option>
      </select>

      <label for="signup-id-number">ID Number</label>
      <input type="text" id="signup-id-number" name="signup-id-number" required>

      <button type="submit">Signup</button>
    </form>
    <p>Already have an account? <a href="login_form.php"><button>Login</button></a></p>

  </div>
 
</body>
</html>