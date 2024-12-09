<?php
require_once '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $role = $_POST['role'];
    $points = $_POST['points'];
    $password = $_POST['password'] ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;

    $sql = "UPDATE users SET email = ?, username = ?, role = ?, points = ?" . ($password ? ", password = ?" : "") . " WHERE id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        if ($password) {
            $stmt->bind_param("sssssi", $email, $username, $role, $points, $password, $id);
        } else {
            $stmt->bind_param("ssssi", $email, $username, $role, $points, $id);
        }

        if ($stmt->execute()) {
            header("Location: manage_users.php");
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}
?>
