<?php
require_once '../includes/auth.php';
requireAuth();

$admin = getCurrentAdmin();

// Handle delete request
if (isset($_GET['delete'])) {
    $post_id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE id = ?");
    $stmt->execute([$post_id]);
    redirect('dashboard.php');
}

// Get all blog posts
$stmt = $pdo->query("SELECT bp.*, a.username as author FROM blog_posts bp 
                     JOIN admins a ON bp.author_id = a.id 
                     ORDER BY bp.created_at DESC");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin_styles.css">
</head>
<body>
    <div class="admin-header">
        <h1>Blog Admin Dashboard</h1>
        <div class="admin-nav">
            <span>Welcome, <?php echo $admin['username']; ?></span>
            <a href="?logout=1" onclick="return confirm('Are you sure?')">Logout</a>
        </div>
    </div>

    <?php if (isset($_GET['logout'])): logout(); endif; ?>

    <div class="admin-container">
        <div class="admin-actions">
            <a href="create_post.php" class="btn-primary">Create New Post</a>
        </div>

        <div class="posts-table">
            <h3>All Blog Posts</h3>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Author</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $post): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($post['title']); ?></td>
                        <td>
                            <span class="status <?php echo $post['status']; ?>">
                                <?php echo ucfirst($post['status']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($post['author']); ?></td>
                        <td><?php echo date('M j, Y', strtotime($post['created_at'])); ?></td>
                        <td class="actions">
                            <a href="../blog_post.php?id=<?php echo $post['id']; ?>" target="_blank">View</a>
                            <a href="edit_post.php?id=<?php echo $post['id']; ?>">Edit</a>
                            <a href="?delete=<?php echo $post['id']; ?>" 
                               onclick="return confirm('Delete this post?')" 
                               class="delete">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>