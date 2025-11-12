<?php
session_start();
require('dbcon.php'); // $con = mysqli_connect(...)

$msg = "";

// Handle form submission
if (isset($_POST['submit'])) {
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $content = mysqli_real_escape_string($con, $_POST['content']);

    // Handle file upload
    $photo_name = "";
    if (!empty($_FILES['photo']['name'])) {
        $photo_name = time() . "_" . basename($_FILES['photo']['name']);
        $target_dir = "uploads/";
        if (!is_dir($target_dir))
            mkdir($target_dir, 0755, true);
        move_uploaded_file($_FILES['photo']['tmp_name'], $target_dir . $photo_name);
    }

    $sql = "INSERT INTO blogs (title, content, photo) VALUES ('$title', '$content', '$photo_name')";
    if (mysqli_query($con, $sql)) {
        $msg = "✅ Blog/news added successfully!";
    } else {
        $msg = "❌ Error: " . mysqli_error($con);
    }

    // Redirect to prevent form resubmission
    $_SESSION['msg'] = $msg;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch all blogs/news
$blogs = mysqli_query($con, "SELECT * FROM blogs ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Blogs/News</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
    body {
        background: linear-gradient(45deg, #a6ac59, #c06a54);
        height: 100vh;
        margin: 0;
    }

    .card {
        margin-bottom: 30px;
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
    }

    .card-img-top {
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
        max-height: 250px;
        object-fit: cover;
    }

    .like-btn {
        cursor: pointer;
        transition: transform 0.2s;
    }

    .like-btn:hover {
        transform: scale(1.1);
    }

    h2 {
        font-weight: 700;
        color: #343a40;
        margin-bottom: 40px;
    }

    .card-title {
        font-weight: 600;
    }

    .card-text {
        color: #495057;
    }

    .text-muted {
        font-size: 0.85rem;
    }

    .footer {
        background: linear-gradient(135deg, #1f1c2c, #928DAB);
        border-top: 3px solid #0d6efd;
    }

    .footer h4 {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        letter-spacing: 1px;
    }

    .footer-link {
        color: #f8f9fa;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    .footer-link:hover {
        color: #0d6efd;
    }

    .social-icons {
        font-size: 1.4rem;
    }

    .social {
        color: #f8f9fa;
        margin: 0 10px;
        transition: transform 0.3s ease, color 0.3s ease;
    }

    .social:hover {
        color: #0d6efd;
        transform: scale(1.2);
    }
</style>

<body class="bg-light">
    <div class="container py-4">

        <!-- Show session message -->
        <?php
        if (isset($_SESSION['msg'])) {
            echo "<div class='alert alert-info text-center'>" . $_SESSION['msg'] . "</div>";
            unset($_SESSION['msg']);
        }
        ?>

        <!-- Add Blog/News -->
        <div class="card p-4 mb-4">
            <h2 class="text-center">Add Blog/News</h2>
            <form method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label>Title:</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Photo:</label>
                    <input type="file" name="photo" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Content:</label>
                    <textarea name="content" rows="6" class="form-control" placeholder="Type your content here..."
                        required></textarea>
                </div>
                <button class="btn btn-primary w-100" name="submit" type="submit">Add</button>
            </form>
        </div>

        <!-- List of Blogs/News -->
        <div class="card p-4">
            <h2 class="text-center">📋 Uploaded Blogs/News</h2>
            <?php if (mysqli_num_rows($blogs) > 0) { ?>
                <table class="table table-bordered text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Photo</th>
                            <th>Content</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($blogs)) { ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['title']) ?></td>
                                <td>
                                    <?php if (!empty($row['photo'])) { ?>
                                        <img src="uploads/<?= $row['photo'] ?>" width="100">
                                    <?php } ?>
                                </td>
                                <td><?= nl2br(htmlspecialchars($row['content'])) ?></td>
                                <td><?= $row['created_at'] ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p class="text-center text-muted">No blogs/news added yet.</p>
            <?php } ?>
        </div>
    </div>
    <footer class="footer text-light mt-5">
        <div class="container py-5 text-center">
            <h4 class="fw-bold mb-3">✨ Abeyaantrix</h4>
            <p class="mb-3">
                <a href="https://abeyaantrix.org/" target="_blank" class="footer-link">abeyaantrix.org</a>
            </p>
            <div class="social-icons mb-3">
                <a href="#" class="social"><i class="bi bi-facebook"></i></a>
                <a href="#" class="social"><i class="bi bi-twitter"></i></a>
                <a href="#" class="social"><i class="bi bi-instagram"></i></a>
                <a href="#" class="social"><i class="bi bi-linkedin"></i></a>
            </div>
            <p class="small mb-0">&copy; <?php echo date("Y"); ?> Abeyaantrix. All Rights Reserved.</p>
        </div>
    </footer>
</body>

</html>