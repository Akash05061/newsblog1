

<?php
session_start();
require("dbcon.php");
if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $sql = "SELECT * FROM users WHERE username='$username' and password='$password'";
    $res = mysqli_query($con, $sql);
    if (mysqli_num_rows($res) > 0) {

        while ($row = mysqli_fetch_assoc($res)) {
            $_SESSION["username"] = $row['username'];
            $_SESSION['role'] = $row['role'];

            if ($row['role'] == 'admin') {
                header('Location:adminhome.php');
                exit;
            } elseif ($row['role'] == 'user') {
                header('Location:userhome.php');
                exit;
            }

        }
    } else {
        echo "invaled username or password...:(";
    }
}


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
