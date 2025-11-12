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