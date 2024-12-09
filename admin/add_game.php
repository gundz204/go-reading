<?php
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $english_word = $_POST['english_word'];
    $indonesian_word = $_POST['indonesian_word'];
    $book_id = $_POST['book_id'];
    $pair_id = random_int(200, 1000); // Generate random Pair ID

    $stmt = $conn->prepare("INSERT INTO games (english_word, indonesian_word, book_id, pair_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssii", $english_word, $indonesian_word, $book_id, $pair_id);
    $stmt->execute();

    header("Location: manage_games.php");
    exit;
}
?>
