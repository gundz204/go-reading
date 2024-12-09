<?php
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $category = $_POST['category'];
    $author = $_POST['author'];
    $content = $_POST['content'];
    $cover = $_FILES['cover'];

    // Jika ada file yang diupload, update cover-nya
    if ($cover['error'] === UPLOAD_ERR_OK) {
        $targetDir = "../uploads/";
        $fileName = basename($cover['name']);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($cover['tmp_name'], $targetFile)) {
            $sql = "UPDATE books SET title=?, category=?, author=?, content=?, image_url=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sssssi', $title, $category, $author, $content, $fileName, $id);
        }
    } else {
        // Update tanpa mengubah cover
        $sql = "UPDATE books SET title=?, category=?, author=?, content=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssi', $title, $category, $author, $content, $id);
    }

    if ($stmt->execute()) {
        header('Location: manage_books.php');
        exit;
    } else {
        echo "Failed to update book.";
    }
}
?>
