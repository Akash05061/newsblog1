<?php
// Hardcode the NEW, REAL credentials
$rds_host = "your-NEW-db.xxxxxx.rds.amazonaws.com";
$rds_user = "your-new-admin-user";
$rds_pass = "your-new-password";
$rds_db   = "blog";

$con = mysqli_connect($rds_host, $rds_user, $rds_pass, $rds_db);

if (!$con) {
    die("Connection Error: " . mysqli_connect_error());
}
?>
