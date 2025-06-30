<?php
require_once '../includes/auth.php';
requireAuth();

$admin = getCurrentAdmin();
$editing = false;
$post = null;

// Check if editing existing post
if (isset($_GET['id'])) {
    $editing = true;
    $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$post) {
        redirect('dashboard.php');
    }
}

$success = '';
$error = '';

if ($_POST) {
    $title = sanitize($_POST['title']);
    $content = $_POST['content']; // Don't sanitize content as it may contain HTML
    $excerpt = sanitize($_POST['excerpt']);
    $status = $_POST['status'];
    
    if (empty($title) || empty($content)) {
        $error = 'Title and content are required';
    } else {
        try {
            if ($editing) {
                $stmt = $pdo->prepare("UPDATE blog_posts SET title = ?, content = ?, excerpt = ?, status = ? WHERE id = ?");
                $stmt->execute([$title, $content, $excerpt, $status, $post['id']]);
                $success = 'Post updated successfully!';
            } else {
                $stmt = $pdo->prepare("INSERT INTO blog_posts (title, content, excerpt, status, author_id) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$title, $content, $excerpt, $status, $admin['id']]);
                $success = 'Post created successfully!';
            }
        } catch (Exception $e) {
            $error = 'Error saving post: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $editing ? 'Edit' : 'Create'; ?> Post</title>
    <link rel="stylesheet" href="admin_styles.css">
</head>
<body>
    <div class="admin-header">
        <h1><?php echo $editing ? 'Edit' : 'Create New'; ?> Blog Post</h1>
        <div class="admin-nav">
            <a href="dashboard.php">← Back to Dashboard</a>
            <a href="?logout=1">Logout</a>
        </div>
    </div>

    <?php if (isset($_GET['logout'])): logout(); endif; ?>

    <div class="admin-container">
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" class="post-form">
            <div class="form-group">
                <label>Title:</label>
                <input type="text" name="title" required 
                       value="<?php echo $post ? htmlspecialchars($post['title']) : ''; ?>">
            </div>

            <div class="form-group">
                <label>Excerpt (Optional):</label>
                <textarea name="excerpt" rows="3"><?php echo $post ? htmlspecialchars($post['excerpt']) : ''; ?></textarea>
            </div>

            <div class="form-group">
                <label>Content:</label>
                <textarea name="content" rows="15" required><?php echo $post ? htmlspecialchars($post['content']) : ''; ?></textarea>
            </div>

            <div class="form-group">
                <label>Status:</label>
                <select name="status">
                    <option value="draft" <?php echo ($post && $post['status'] == 'draft') ? 'selected' : ''; ?>>Draft</option>
                    <option value="published" <?php echo ($post && $post['status'] == 'published') ? 'selected' : ''; ?>>Published</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <?php echo $editing ? 'Update' : 'Create'; ?> Post
                </button>
                <a href="dashboard.php" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
