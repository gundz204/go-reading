<?php
require_once '../includes/db.php';

// Validasi input
$title = $_POST['title'];
$category = $_POST['category'];
$author = $_POST['author'];
$content = $_POST['content']; // Tangkap content
$cover = $_FILES['cover'];

// Proses upload cover
$target_dir = "../uploads/";
$cover_name = basename($cover["name"]);
$target_file = $target_dir . $cover_name;
move_uploaded_file($cover["tmp_name"], $target_file);

// Simpan ke database
$sql = "INSERT INTO books (title, category, author, content, image_url) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $title, $category, $author, $content, $cover_name);

if ($stmt->execute()) {
    header("Location: manage_books.php?success=1");
} else {
    header("Location: manage_books.php?error=1");
}
?>
