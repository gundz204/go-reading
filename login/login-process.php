<?php
// Include database connection
include('../includes/db.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $row['password'])) {
            // Set session with user_id
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $username;
            header("Location: ../pages/home.php"); // Redirect to the home page
            exit();
        } else {
            // Invalid password, store error message in session
            $_SESSION['error'] = "Invalid password!";
            header("Location: index.php");
            exit();
        }
    } else {
        // Username not found, store error message in session
        $_SESSION['error'] = "Username not found!";
        header("Location: index.php");
        exit();
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
