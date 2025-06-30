<?php
require_once 'includes/config.php';

// Get post ID from URL
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$post_id) {
    redirect('index.php');
}

// Get the blog post
$stmt = $pdo->prepare("SELECT bp.*, a.username as author FROM blog_posts bp 
                       JOIN admins a ON bp.author_id = a.id 
                       WHERE bp.id = ? AND bp.status = 'published'");
$stmt->execute([$post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    redirect('index.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?> - My Blog</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header class="blog-header">
        <div class="container">
            <h1><a href="index.php">My Blog</a></h1>
            <nav>
                <a href="index.php">Home</a>
                <a href="admin/login.php">Admin</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <article class="single-post">
            <header class="post-header">
                <h1><?php echo htmlspecialchars($post['title']); ?></h1>
                <div class="post-meta">
                    <span>By <?php echo htmlspecialchars($post['author']); ?></span>
                    <span><?php echo date('F j, Y', strtotime($post['created_at'])); ?></span>
                    <?php if ($post['updated_at'] != $post['created_at']): ?>
                        <span>(Updated: <?php echo date('F j, Y', strtotime($post['updated_at'])); ?>)</span>
                    <?php endif; ?>
                </div>
            </header>
            
            <div class="post-content">
                <?php echo nl2br($post['content']); ?>
            </div>
            
            <footer class="post-footer">
                <a href="index.php" class="back-link">← Back to Blog</a>
            </footer>
        </article>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> My Blog. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>