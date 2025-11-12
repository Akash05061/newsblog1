<?php
session_start();
require('dbcon.php'); // $con = mysqli_connect(...)

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Handle like
if (isset($_GET['like'])) {
    $id = (int) $_GET['like'];
    $sql = "UPDATE blogs SET likes = likes + 1 WHERE id = $id";
    mysqli_query($con, $sql);

    // Redirect to prevent multiple likes on refresh
    header("Location: userhome.php");
    exit();
}

// Fetch all blogs/news
$blogs = mysqli_query($con, "SELECT * FROM blogs ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Home - Blogs/News</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
           background: linear-gradient(45deg, #a6ac59, #c06a54);
            height: 100vh;
            margin: 0;
        }
        .card {
            margin-bottom: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0,0,0,0.15);
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
</head>
<body>
<div class="container py-5">
    <h2 class="text-center">📚 Latest Blogs & News</h2>

    <?php if(mysqli_num_rows($blogs) > 0): ?>
        <div class="row">
            <?php while($row = mysqli_fetch_assoc($blogs)): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card">
                        <?php if(!empty($row['photo'])): ?>
                            <img src="uploads/<?= $row['photo'] ?>" class="card-img-top" alt="Photo" style="border-radius: 5px;">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
                            <p class="card-text"><?= nl2br(htmlspecialchars($row['content'])) ?></p>
                            <p class="text-muted">Posted on: <?= $row['created_at'] ?></p>
                            <a href="?like=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary like-btn">
                                👍 Like (<?= $row['likes'] ?>)
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p class="text-center text-muted">No blogs/news available.</p>
    <?php endif; ?>
    
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
