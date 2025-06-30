<?php

require_once 'config.php';


function getBlogPosts($status = null, $limit = null, $offset = 0) {
    global $pdo;
    
    $sql = "SELECT bp.*, a.username as author_name, a.email as author_email 
            FROM blog_posts bp 
            JOIN admins a ON bp.author_id = a.id";
    
    $params = [];
    
    if ($status) {
        $sql .= " WHERE bp.status = ?";
        $params[] = $status;
    }
    
    $sql .= " ORDER BY bp.created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getBlogPost($id, $published_only = true) {
    global $pdo;
    
    $sql = "SELECT bp.*, a.username as author_name, a.email as author_email 
            FROM blog_posts bp 
            JOIN admins a ON bp.author_id = a.id 
            WHERE bp.id = ?";
    
    $params = [$id];
    
    if ($published_only) {
        $sql .= " AND bp.status = 'published'";
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/*
  Create new blog post
 */
function createBlogPost($title, $content, $excerpt = '', $status = 'draft', $author_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO blog_posts (title, content, excerpt, status, author_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $content, $excerpt, $status, $author_id]);
        return $pdo->lastInsertId();
    } catch (Exception $e) {
        error_log("Error creating blog post: " . $e->getMessage());
        return false;
    }
}

/**
 * Update existing blog post
 */
function updateBlogPost($id, $title, $content, $excerpt = '', $status = 'draft') {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("UPDATE blog_posts SET title = ?, content = ?, excerpt = ?, status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        return $stmt->execute([$title, $content, $excerpt, $status, $id]);
    } catch (Exception $e) {
        error_log("Error updating blog post: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete blog post
 */
function deleteBlogPost($id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE id = ?");
        return $stmt->execute([$id]);
    } catch (Exception $e) {
        error_log("Error deleting blog post: " . $e->getMessage());
        return false;
    }
}


function getBlogStats() {
    global $pdo;
    
    $stats = [];
    
    // Total posts
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM blog_posts");
    $stats['total_posts'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Published posts
    $stmt = $pdo->query("SELECT COUNT(*) as published FROM blog_posts WHERE status = 'published'");
    $stats['published_posts'] = $stmt->fetch(PDO::FETCH_ASSOC)['published'];
    
    // Draft posts
    $stmt = $pdo->query("SELECT COUNT(*) as drafts FROM blog_posts WHERE status = 'draft'");
    $stats['draft_posts'] = $stmt->fetch(PDO::FETCH_ASSOC)['drafts'];
    
    // Recent posts (last 30 days)
    $stmt = $pdo->query("SELECT COUNT(*) as recent FROM blog_posts WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $stats['recent_posts'] = $stmt->fetch(PDO::FETCH_ASSOC)['recent'];
    
    return $stats;
}

/**
 * Search blog posts
 */
function searchBlogPosts($query, $published_only = true) {
    global $pdo;
    
    $sql = "SELECT bp.*, a.username as author_name 
            FROM blog_posts bp 
            JOIN admins a ON bp.author_id = a.id 
            WHERE (bp.title LIKE ? OR bp.content LIKE ? OR bp.excerpt LIKE ?)";
    
    $searchTerm = "%$query%";
    $params = [$searchTerm, $searchTerm, $searchTerm];
    
    if ($published_only) {
        $sql .= " AND bp.status = 'published'";
    }
    
    $sql .= " ORDER BY bp.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Generate excerpt from content
 */
function generateExcerpt($content, $length = 200) {
    $content = strip_tags($content);
    $content = preg_replace('/\s+/', ' ', $content);
    
    if (strlen($content) <= $length) {
        return $content;
    }
    
    $excerpt = substr($content, 0, $length);
    $lastSpace = strrpos($excerpt, ' ');
    
    if ($lastSpace !== false) {
        $excerpt = substr($excerpt, 0, $lastSpace);
    }
    
    return $excerpt . '...';
}

/**
 * Format date for display
 */
function formatDate($date, $format = 'F j, Y') {
    return date($format, strtotime($date));
}

/**
 * Get time ago string
 */
function timeAgo($date) {
    $time = time() - strtotime($date);
    
    if ($time < 60) return 'just now';
    if ($time < 3600) return floor($time/60) . ' minutes ago';
    if ($time < 86400) return floor($time/3600) . ' hours ago';
    if ($time < 2592000) return floor($time/86400) . ' days ago';
    if ($time < 31536000) return floor($time/2592000) . ' months ago';
    
    return floor($time/31536000) . ' years ago';
}

/**
 * Validate blog post data
 */
function validateBlogPost($title, $content, $status = 'draft') {
    $errors = [];
    
    if (empty(trim($title))) {
        $errors[] = 'Title is required';
    } elseif (strlen($title) > 255) {
        $errors[] = 'Title must be less than 255 characters';
    }
    
    if (empty(trim($content))) {
        $errors[] = 'Content is required';
    }
    
    if (!in_array($status, ['draft', 'published'])) {
        $errors[] = 'Invalid status';
    }
    
    return $errors;
}

/**
 * Check if slug exists
 */
function slugExists($slug, $exclude_id = null) {
    global $pdo;
    
    $sql = "SELECT id FROM blog_posts WHERE slug = ?";
    $params = [$slug];
    
    if ($exclude_id) {
        $sql .= " AND id != ?";
        $params[] = $exclude_id;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch() !== false;
}

/**
 * Generate unique slug from title
 */
function generateSlug($title, $exclude_id = null) {
    $slug = strtolower(trim($title));
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    $slug = trim($slug, '-');
    
    $original_slug = $slug;
    $counter = 1;
    
    while (slugExists($slug, $exclude_id)) {
        $slug = $original_slug . '-' . $counter;
        $counter++;
    }
    
    return $slug;
}

/**
 * Get related posts
 */
function getRelatedPosts($post_id, $limit = 3) {
    global $pdo;
    
    // Simple implementation - get recent posts excluding current
    $stmt = $pdo->prepare("SELECT bp.*, a.username as author_name 
                          FROM blog_posts bp 
                          JOIN admins a ON bp.author_id = a.id 
                          WHERE bp.id != ? AND bp.status = 'published' 
                          ORDER BY bp.created_at DESC 
                          LIMIT ?");
    $stmt->execute([$post_id, $limit]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Count total posts by status
 */
function countPostsByStatus($status = null) {
    global $pdo;
    
    if ($status) {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM blog_posts WHERE status = ?");
        $stmt->execute([$status]);
    } else {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM blog_posts");
    }
    
    return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
}

/**
 * Get posts by date range
 */
function getPostsByDateRange($start_date, $end_date, $status = 'published') {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT bp.*, a.username as author_name 
                          FROM blog_posts bp 
                          JOIN admins a ON bp.author_id = a.id 
                          WHERE bp.created_at BETWEEN ? AND ? 
                          AND bp.status = ? 
                          ORDER BY bp.created_at DESC");
    $stmt->execute([$start_date, $end_date, $status]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Backup blog posts to JSON
 */
function backupBlogPosts() {
    $posts = getBlogPosts();
    $backup_data = [
        'backup_date' => date('Y-m-d H:i:s'),
        'posts' => $posts
    ];
    
    $filename = 'blog_backup_' . date('Y-m-d_H-i-s') . '.json';
    $backup_dir = '../backups/';
    
    if (!file_exists($backup_dir)) {
        mkdir($backup_dir, 0755, true);
    }
    
    file_put_contents($backup_dir . $filename, json_encode($backup_data, JSON_PRETTY_PRINT));
    return $filename;
}

/**
 * Log admin activity
 */
function logAdminActivity($admin_id, $action, $details = '') {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO admin_logs (admin_id, action, details, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$admin_id, $action, $details]);
    } catch (Exception $e) {
        error_log("Error logging admin activity: " . $e->getMessage());
    }
}

/**
 * Send notification email (if configured)
 */
function sendNotificationEmail($to, $subject, $message) {
    // Basic email function - configure with your SMTP settings
    $headers = [
        'From: noreply@yoursite.com',
        'Reply-To: noreply@yoursite.com',
        'Content-Type: text/html; charset=UTF-8'
    ];
    
    return mail($to, $subject, $message, implode("\r\n", $headers));
}

/**
 * Check if user has permission
 */
function hasPermission($required_permission) {
    // Simple permission check - can be expanded
    return isLoggedIn(); // For now, all logged-in admins have all permissions
}

/**
 * Clean old drafts (utility function)
 */
function cleanOldDrafts($days = 30) {
    global $pdo;
    
    $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE status = 'draft' AND created_at < DATE_SUB(NOW(), INTERVAL ? DAY)");
    return $stmt->execute([$days]);
}
?>