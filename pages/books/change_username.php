<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require '../../includes/db.php';
$id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUsername = htmlspecialchars($_POST['username']);
    $query = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
    $query->bind_param('si', $newUsername, $id);
    $query->execute();

    header("Location: profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Change Username</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <form method="POST">
            <label for="username" class="form-label">New Username:</label>
            <input type="text" name="username" id="username" class="form-control" required>
            <button type="submit" class="btn btn-primary mt-3">Update</button>
        </form>
    </div>
</body>
</html>
