<?php
// --- THESE LINES SHOW REAL ERRORS INSTEAD OF A 500 PAGE ---
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require("dbcon.php"); // Connects to the database

// This block will only run AFTER the user clicks 'submit'
if (isset($_POST['submit'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    // --- SECURE LOGIN USING PREPARED STATEMENTS ---
    // This prevents SQL Injection
    
    // 1. Prepare the SQL statement with a placeholder (?)
    $stmt = mysqli_prepare($con, "SELECT username, role, password FROM users WHERE username = ?");
    
    // 2. Bind the user's input (the 's' means 'string')
    mysqli_stmt_bind_param($stmt, "s", $username);
    
    // 3. Execute the statement
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // 4. Check if we found a user with that username
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // 5. Verify the password
        // !! SECURITY WARNING !!
        // Your current code compares plain text. This is VERY insecure.
        // You should store passwords using password_hash()
        // and check them using password_verify($password, $row['password'])
        if ($password == $row['password']) { 
            
            // Password is correct, set sessions
            $_SESSION["username"] = $row['username'];
            $_SESSION['role'] = $row['role'];

            // Redirect based on role
            if ($row['role'] == 'admin') {
                header('Location: adminhome.php');
                exit;
            } elseif ($row['role'] == 'user') {
                header('Location: userhome.php');
                exit;
            }

        } else {
            // Password was wrong
            $error_message = "Invalid username or password!";
        }
    } else {
        // No user with that username was found
        $error_message = "Invalid username or password!";
    }
    
    mysqli_stmt_close($stmt);

} // End of submit block

mysqli_close($con); // Close the database connection
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign-in Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #fff8dc;
      height: 100vh;
      margin: 0;
    }
    .card {
      max-width: 400px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.6);
      border-radius: 12px;
      overflow: hidden;
    }
    .card-header {
      background: linear-gradient(45deg, #7b6faf, #928DAB);
      color: white;
    }
    .card-body {
      background: linear-gradient(45deg, #a6ac59, #c06a54);
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="card mx-auto mt-5 ">
      <div class="card-header text-center">
        <h2>Sign-in Page</h2>
      </div>
      <div class="card-body">

        <?php
        if (isset($error_message)) {
            echo '<div class="alert alert-danger">' . htmlspecialchars($error_message) . '</div>';
        }
        ?>

        <form method="post">
          <div class="mb-3">
            <label for="username" class="form-label">Username:</label>
            <input type="text" id="username" class="form-control" required name="username">
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password:</label>
            <input type="password" id="password" class="form-control" required name="password">
          </div>
          <div class="mb-2">
            <button class="btn btn-primary w-100" type="submit" name="submit">Login</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>

</html>
