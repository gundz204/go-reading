<?php
// Include database connection
include('../includes/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "Passwords do not match! <a href='register.php'>Go back</a>";
        exit;
    }

    // Encrypt password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email or username already exists
    $sql = "SELECT * FROM users WHERE email = '$email' OR username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "Email or Username already taken!";
    } else {
        // Insert new user with default values for role, points, and theme
        $sql = "INSERT INTO users (email, username, password, role, points, theme) 
                VALUES ('$email', '$username', '$hashed_password', 'user', 0, 'light')";

        if ($conn->query($sql) === TRUE) {
            // Redirect to registration page with success message
            header("Location: register.php?success=1");
            exit;
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
?>
