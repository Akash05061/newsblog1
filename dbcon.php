<?php
// --- dbcon.php (Updated for Docker) ---

// Get credentials from Environment Variables
// We will pass these in with the 'docker run -e ...' command
$rds_host = getenv('DB_HOST');
$rds_user = getenv('DB_USER');
$rds_pass = getenv('DB_PASS');
$rds_db   = getenv('DB_NAME');

// Fallback for local development (optional)
if (empty($rds_host)) {
    $rds_host = 'database-1.cb80q24w8pym.us-east-1.rds.amazonaws.com';
    $rds_user = 'root';
    $rds_pass = 'aaaaaaaa';
    $rds_db   = 'blog';
}

$con = mysqli_connect($rds_host, $rds_user, $rds_pass, $rds_db);

// This 'if' block is very important for debugging!
if (!$con) {
    die("Connection Error (" . mysqli_connect_errno() . "): " . mysqli_connect_error());
}
?>
