<?php
require_once '../includes/db.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $english_word = $_POST['english_word'];
    $indonesian_word = $_POST['indonesian_word'];
    $book_id = $_POST['book_id'];
    $pair_id = $_POST['pair_id'];

    // Update game record in the database
    $sql = "UPDATE games 
            SET english_word = ?, indonesian_word = ?, book_id = ?, pair_id = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisi", $english_word, $indonesian_word, $book_id, $pair_id, $id);

    if ($stmt->execute()) {
        header("Location: manage_games.php");
        exit;
    } else {
        echo "Error updating game: " . $conn->error;
    }
}
?>
