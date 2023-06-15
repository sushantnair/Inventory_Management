<!DOCTYPE html>
<html>
<head>
  <title>Login Page</title>
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
    <h1><u>KJSCE Inventory Management</u></h1>
    <h2>Login</h2>
    <form action="login.php" method="POST">
      <label for="login-email">Email</label>
      <input type="email" id="login-email" name="login-email" required>

      <label for="login-password">Password</label>
      <input type="password" id="login-password" name="login-password" required>

      <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="signup_form.php"><button>Signup</button></a></p>
  
  </div>
</body>
</html>