<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['content']) && !empty(trim($_POST['content']))) {
        $content = trim($_POST['content']);
        
        try {
            $stmt = $pdo->prepare('INSERT INTO posts (user_id, content, created_at) VALUES (?, ?, NOW())');
            $stmt->execute([$_SESSION['user_id'], $content]);
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
}

try {
    $stmt = $pdo->query('SELECT posts.content, posts.created_at, users.username FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC');
    $posts = $stmt->fetchAll();
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
    $posts = [];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Social Network</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Materialize CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 50px;
        }
        .container {
            max-width: 600px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="center-align">Welcome!</h1>
        <p class="right-align"><a href="logout.php" class="btn btn-danger">Logout</a></p>
        <form method="POST">
            <div class="input-field">
                <textarea name="content" class="materialize-textarea" placeholder="What's on your mind?" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Post</button>
        </form>
        <h2 class="center-align">Posts</h2>
        <?php if (!empty($posts)): ?>
            <?php foreach ($posts as $post): ?>
                <div class="card-panel">
                    <p><strong><?php echo htmlspecialchars($post['username'], ENT_QUOTES, 'UTF-8'); ?>:</strong> <?php echo htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><small><?php echo htmlspecialchars($post['created_at'], ENT_QUOTES, 'UTF-8'); ?></small></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No posts available.</p>
        <?php endif; ?>
    </div>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <!-- Materialize JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>
