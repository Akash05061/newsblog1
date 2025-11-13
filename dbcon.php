<?php
// Hardcode the NEW, REAL credentials
$rds_host = "database-1.cb80q24w8pym.us-east-1.rds.amazonaws.com";
$rds_user = "root";
$rds_pass = "aaaaaaaa";
$rds_db   = "blog";

$con = mysqli_connect($rds_host, $rds_user, $rds_pass, $rds_db);

if (!$con) {
    die("Connection Error: " . mysqli_connect_error());
}
?>
