<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require '../../includes/db.php';

// Proses unggah gambar
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_SESSION['user_id'];

    // Periksa apakah file diunggah
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $uploadDir = 'uploads/';
        $fileName = basename($_FILES['profile_picture']['name']);
        $fileTmp = $_FILES['profile_picture']['tmp_name'];
        $fileSize = $_FILES['profile_picture']['size'];
        $fileType = mime_content_type($fileTmp);

        // Validasi tipe file (hanya gambar)
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($fileType, $allowedTypes)) {
            echo "Invalid file type. Only JPEG, PNG, and GIF are allowed.";
            exit();
        }

        // Validasi ukuran file (maksimal 2MB)
        if ($fileSize > 2 * 1024 * 1024) {
            echo "File size exceeds the maximum limit of 2MB.";
            exit();
        }

        // Pindahkan file ke folder uploads
        $newFileName = uniqid('profile_', true) . '.' . pathinfo($fileName, PATHINFO_EXTENSION);
        $uploadPath = $uploadDir . $newFileName;

        if (move_uploaded_file($fileTmp, $uploadPath)) {
            // Simpan path gambar ke database
            $query = $conn->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
            $query->bind_param('si', $newFileName, $userId);
            if ($query->execute()) {
                header("Location: profile.php");
                exit();
            } else {
                echo "Failed to update profile picture in the database.";
            }
        } else {
            echo "Failed to upload the file.";
        }
    } else {
        echo "No file uploaded or an error occurred.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Picture</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Change Profile Picture</h1>
        <form action="change_picture.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="profile_picture" class="form-label">Upload New Picture</label>
                <input type="file" name="profile_picture" id="profile_picture" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
    </div>
</body>
</html>
