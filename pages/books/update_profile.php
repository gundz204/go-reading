<?php
session_start();
require '../../includes/db.php'; // File koneksi database

$user_id = $_SESSION['user_id'];

// Validasi input
$username = trim($_POST['username']);
$password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
$profile_image = null;

if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == UPLOAD_ERR_OK) {
    $image_tmp = $_FILES['profile_image']['tmp_name'];
    $image_name = uniqid() . '_' . $_FILES['profile_image']['name'];
    $upload_dir = 'uploads/';
    $profile_image = $upload_dir . $image_name;

    // Pindahkan gambar ke folder uploads
    if (!move_uploaded_file($image_tmp, $profile_image)) {
        die("Failed to upload image.");
    }
}

// Update data
try {
    $sql = "UPDATE users SET username = ?, ";
    $params = [$username];

    if ($password) {
        $sql .= "password = ?, ";
        $params[] = $password;
    }

    if ($profile_image) {
        $sql .= "profile_image = ?, ";
        $params[] = $profile_image;
    }

    $sql = rtrim($sql, ", ");
    $sql .= " WHERE id = ?";
    $params[] = $user_id;

    $query = $conn->prepare($sql);
    $query->execute($params);

    // Redirect setelah berhasil
    header("Location: profile.php");
    exit();
} catch (PDOException $e) {
    die("Error updating profile: " . $e->getMessage());
}
?>
