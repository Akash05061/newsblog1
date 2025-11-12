<?php
// --- THIS IS YOUR NEW, COMPLETE adminhome.php FILE ---

// --- THESE LINES SHOW REAL ERRORS INSTEAD OF A 500 PAGE ---
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require('dbcon.php'); // $con = mysqli_connect(...)
$msg = "";

// Check if user is logged in AND is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // If not an admin, send them to the login page
    header("Location: login.php"); // Assuming your login file is login.php
    exit();
}

// Handle form submission
if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $photo_name = "";
    $upload_ok = 1; // A flag to check if we can proceed

    // --- SECURE FILE UPLOAD CHECK ---
    if (!empty($_FILES['photo']['name'])) {
        $target_dir = "uploads/"; // We already created this folder
        $photo_name = time() . "_" . basename($_FILES['photo']['name']);
        $target_file = $target_dir . $photo_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is an actual image
        $check = getimagesize($_FILES['photo']['tmp_name']);
        if ($check === false) {
            $msg = "❌ Error: File is not an image.";
            $upload_ok = 0;
        }

        // Allow only certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $msg = "❌ Error: Only JPG, JPEG, PNG & GIF files are allowed.";
            $upload_ok = 0;
        }

        // Try to move the file if all checks passed
        if ($upload_ok == 1) {
            if (!move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
                $msg = "❌ Error: There was an error uploading your file.";
                $upload_ok = 0;
            }
        }
    } // End of file upload check

    // --- SECURE DATABASE INSERT (PREPARED STATEMENT) ---
    // Only proceed if the file upload was successful (or if no file was uploaded)
    if ($upload_ok == 1) {
        
        // 1. Prepare the statement with placeholders (?)
        $stmt = mysqli_prepare($con, "INSERT INTO blogs (title, content, photo) VALUES (?, ?, ?)");
        
        // 2. Bind the variables (s = string)
        mysqli_stmt_bind_param($stmt, "sss", $title, $content, $photo_name);
        
        // 3. Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            $msg = "✅ Blog/news added successfully!";
        } else {
            $msg = "❌ Error: " . mysqli_error($con);
        }
        mysqli_stmt_close($stmt);

    } // End of $upload_ok check

    // Redirect to prevent form resubmission
    $_SESSION['msg'] = $msg;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
} // End of POST submit check

// Fetch all blogs/news to display in the table
$blogs = mysqli_query($con, "SELECT * FROM blogs ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Blogs/News</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Your CSS styles are fine, no changes needed */
        body { background: linear-gradient(45deg, #a6ac59, #c06a54); margin: 0; }
        .card { margin-bottom: 30px; border-radius: 15px; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1); }
        .card-img-top { border-top-left-radius: 15px; border-top-right-radius: 15px; max-height: 250px; object-fit: cover; }
        h2 { font-weight: 700; color: #343a40; margin-bottom: 40px; }
        .card-title { font-weight: 600; }
        .footer { background: linear-gradient(135deg, #1f1c2c, #928DAB); border-top: 3px solid #0d6efd; }
        .footer-link { color: #f8f9fa; text-decoration: none; font-weight: 500; }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">

        <?php
        if (isset($_SESSION['msg'])) {
            // Check if message is an error or success for coloring
            $alert_class = (strpos($_SESSION['msg'], 'Error') !== false) ? 'alert-danger' : 'alert-success';
            echo "<div class='alert " . $alert_class . " text-center'>" . $_SESSION['msg'] . "</div>";
            unset($_SESSION['msg']);
        }
        ?>

        <div class="card p-4 mb-4">
            <h2 class="text-center">Add Blog/News</h2>
            <form method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Title:</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Photo:</label>
                    <input type="file" name="photo" class="form-control" accept="image/png, image/jpeg, image/gif">
                </div>
                <div class="mb-3">
                    <label class="form-label">Content:</label>
                    <textarea name="content" rows="6" class="form-control" placeholder="Type your content here..." required></textarea>
                </div>
                <button class="btn btn-primary w-100" name="submit" type="submit">Add Post</button>
            </form>
        </div>

        <div class="card p-4">
            <h2 class="text-center">📋 Uploaded Blogs/News</h2>
            <?php if (mysqli_num_rows($blogs) > 0) : ?>
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Photo</th>
                                <th>Content</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($blogs)) : ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['title']) ?></td>
                                    <td>
                                        <?php if (!empty($row['photo'])) : ?>
                                            <img src="uploads/<?= htmlspecialchars($row['photo']) ?>" width="100">
                                        <?php endif; ?>
                                    </td>
                                    <td><?= nl2br(htmlspecialchars(substr($row['content'], 0, 100))) ?>...</td>
                                    <td><?= $row['created_at'] ?></td>
                                    <td>
                                        <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else : ?>
                <p class="text-center text-muted">No blogs/news added yet.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <footer class="footer text-light mt-5">
        <div class="container py-5 text-center">
            <h4 class="fw-bold mb-3">✨ Abeyaantrix</h4>
            <p><a href="https://abeyaantrix.org/" target="_blank" class="footer-link">abeyaantrix.org</a></p>
            <p class="small mb-0">&copy; <?php echo date("Y"); ?> Abeyaantrix. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>
