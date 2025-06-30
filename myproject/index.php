<?php
require_once 'includes/config.php';

// Get published blog posts
$stmt = $pdo->query("SELECT bp.*, a.username as author FROM blog_posts bp 
                     JOIN admins a ON bp.author_id = a.id 
                     WHERE bp.status = 'published' 
                     ORDER BY bp.created_at DESC");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Blog</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header class="blog-header">
        <div class="container">
            <h1>My Blog</h1>
            <nav>
                <a href="index.php">Home</a>
                <a href="admin/login.php">Admin</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="blog-posts">
            <?php if (empty($posts)): ?>
                <div class="no-posts">
                    <h2>No posts yet</h2>
                    <p>Check back later for new content!</p>
                </div>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <article class="blog-post">
                        <h2><a href="blog_post.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></h2>
                        
                        <div class="post-meta">
                            <span>By <?php echo htmlspecialchars($post['author']); ?></span>
                            <span><?php echo date('F j, Y', strtotime($post['created_at'])); ?></span>
                        </div>
                        
                        <div class="post-excerpt">
                            <?php if ($post['excerpt']): ?>
                                <p><?php echo htmlspecialchars($post['excerpt']); ?></p>
                            <?php else: ?>
                                <p><?php echo substr(strip_tags($post['content']), 0, 200) . '...'; ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <a href="blog_post.php?id=<?php echo $post['id']; ?>" class="read-more">Read More</a>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> My Blog. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>