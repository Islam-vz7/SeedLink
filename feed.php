<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: register_login.php");
    exit();
}

// Include configuration
require 'config_reg.php';

// Fetch the logged-in user's details
$username = $_SESSION['username'];
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();
$user_id = $user['id'];

// Handle new post submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_post'])) {
    $caption = $_POST['caption'];
    $image = '';

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $image_name = basename($_FILES['image']['name']);
        $image_path = $upload_dir . uniqid() . '_' . $image_name;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
            $image = $image_path;
        } else {
            echo "Error uploading image.";
        }
    }

    // Insert post into the database
    if (!empty($image)) {
        $stmt = $pdo->prepare("INSERT INTO posts (user_id, image, caption) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $image, $caption]);
    }
}

// Handle new comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $post_id = $_POST['post_id'];
    $comment = $_POST['comment'];

    // Insert comment into the database
    $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)");
    $stmt->execute([$post_id, $user_id, $comment]);
}

// Fetch all posts with their comments
$stmt = $pdo->query("
    SELECT posts.*, users.username 
    FROM posts 
    JOIN users ON posts.user_id = users.id 
    ORDER BY posts.created_at DESC
");
$posts = $stmt->fetchAll();

// Fetch comments for each post
foreach ($posts as &$post) {
    $stmt = $pdo->prepare("
        SELECT comments.*, users.username 
        FROM comments 
        JOIN users ON comments.user_id = users.id 
        WHERE comments.post_id = ? 
        ORDER BY comments.created_at ASC
    ");
    $stmt->execute([$post['id']]);
    $post['comments'] = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SeedLink - Feed</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">SeedLink</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container">
        <h1 class="text-center">🌱 SeedLink - Feed</h1>
        <div class="row mt-4">
            <div class="col-md-8 offset-md-2">
                <!-- Create Post Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        Create a New Post
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="caption">Caption:</label>
                                <textarea class="form-control" id="caption" name="caption" rows="3" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="image">Upload Image:</label>
                                <input type="file" class="form-control-file" id="image" name="image" accept="image/*" required>
                            </div>
                            <button type="submit" name="create_post" class="btn btn-primary">Post</button>
                        </form>
                    </div>
                </div>

                <!-- Display Posts -->
                <?php foreach ($posts as $post): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            Posted by <?php echo htmlspecialchars($post['username']); ?>
                        </div>
                        <div class="card-body">
                            <img src="<?php echo htmlspecialchars($post['image']); ?>" class="img-fluid mb-3" alt="Post Image">
                            <p><?php echo htmlspecialchars($post['caption']); ?></p>
                            <hr>

                            <!-- Display Comments -->
                            <h6>Comments:</h6>
                            <?php foreach ($post['comments'] as $comment): ?>
                                <div class="mb-2">
                                    <strong><?php echo htmlspecialchars($comment['username']); ?>:</strong>
                                    <span><?php echo htmlspecialchars($comment['comment']); ?></span>
                                </div>
                            <?php endforeach; ?>

                            <!-- Add Comment Form -->
                            <form method="POST" class="mt-3">
                                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                <div class="form-group">
                                    <textarea class="form-control" name="comment" rows="2" placeholder="Add a comment..." required></textarea>
                                </div>
                                <button type="submit" name="add_comment" class="btn btn-secondary">Comment</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>