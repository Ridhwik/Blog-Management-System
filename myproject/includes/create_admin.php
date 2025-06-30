<?php
// create_admin.php - Run this once to create admin account
session_start();
require_once 'config.php';

// Admin details
$username = 'admin';
$email = 'admin@example.com';
$password = 'admin123';

try {
    // Check if admin already exists
    $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE username = ? OR email = ?");
    $check_stmt->execute([$username, $email]);
    
    if ($check_stmt->fetchColumn() > 0) {
        echo "<h2>Admin account already exists!</h2>";
        echo "<p>Username: <strong>$username</strong></p>";
        echo "<p>Password: <strong>$password</strong></p>";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert admin
        $stmt = $pdo->prepare("INSERT INTO admins (username, email, password_hash) VALUES (?, ?, ?)");
        
        if ($stmt->execute([$username, $email, $hash])) {
            echo "<h2>✅ Admin account created successfully!</h2>";
            echo "<p><strong>Login Details:</strong></p>";
            echo "<p>Username: <strong>$username</strong></p>";
            echo "<p>Email: <strong>$email</strong></p>";
            echo "<p>Password: <strong>$password</strong></p>";
            echo "<p><a href='admin/login.php'>Go to Login Page</a></p>";
        } else {
            echo "<h2>❌ Error creating admin account</h2>";
            print_r($stmt->errorInfo());
        }
    }
    
} catch (PDOException $e) {
    echo "<h2>❌ Database Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p><strong>Make sure:</strong></p>";
    echo "<ul>";
    echo "<li>Database connection is working</li>";
    echo "<li>The 'admins' table exists</li>";
    echo "<li>Database credentials are correct</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<p><strong>⚠️ Important:</strong> Delete this file after creating the admin account for security!</p>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 40px; }
h2 { color: #333; }
p { margin: 10px 0; }
ul { margin: 10px 0; }
a { color: #007cba; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>