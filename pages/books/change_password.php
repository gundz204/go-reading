<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require '../../includes/db.php';
$id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $query = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $query->bind_param('si', $newPassword, $id);
    $query->execute();

    header("Location: profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Change Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <form method="POST">
            <label for="password" class="form-label">New Password:</label>
            <input type="password" name="password" id="password" class="form-control" required>
            <button type="submit" class="btn btn-primary mt-3">Update</button>
        </form>
    </div>
</body>
</html>
