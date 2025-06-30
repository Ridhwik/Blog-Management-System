# Blog-Management-System

# 📝 Simple PHP Blog System (Admin Only)

Hey there! 👋  
This is a simple blog system made with **PHP and MySQL** where only the **admin** can add, edit, and delete blog posts

Great if you're learning PHP or want to build your own basic blog website.

---

## 📂 Project Folder Structure

Here’s how everything is organized:

/blog-system/

├── admin/ → Admin panel (login, dashboard, create/edit post)

├── includes/ → Config and helper files

├── assets/ → CSS & JS files for styling and frontend

├── uploads/ → Where uploaded blog images are stored

├── index.php → Homepage (lists all blogs)

└── blog_post.php → Single blog view


## 🔧 What You Can Do

- Only admins can log in and manage blog posts ✅
- Add/edit/delete blog posts 📝
- Clean structure and easy to understand 💡

---

## 🛠️ How to Run It (Locally)

Follow these steps to get it up and running on your computer:

### 1️⃣ Clone or Download

2️⃣ Set Up Database
Open phpMyAdmin or any MySQL tool.

Create a new database (example: blog_db)

Run the SQL script below to create the blog_posts table:

<pre> ```CREATE TABLE `blog_posts` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `image_path` VARCHAR(255),
  `author_id` INT(11) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);
  ``` </pre>

3️⃣ Update Config File

Go to:

includes/config.php
And update your database login info:

<pre> ```$conn = mysqli_connect("localhost", "your_mysql_username", "your_mysql_password", "blog_db"); ``` </pre>


5️⃣ Run It on Localhost
If you're using XAMPP:

Move the project folder into htdocs/

Start Apache and MySQL from XAMPP

Go to your browser and open:

http://localhost/{location}/admin/login.php



🔐 Default Admin Login

$username = 'admin';

$password = 'admin123'; // Use hashed password in real apps!


 Note: You can also run create_admin.php to create a default admin 


 

 ✌️ That's It!
This project is just a start. It's simple and clean, so feel free to add your own ideas and make it awesome.

