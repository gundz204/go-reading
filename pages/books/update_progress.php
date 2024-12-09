<?php
session_start();
include('../../includes/db.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit();
}

$userId = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);
$bookId = $data['bookId'] ?? 0;
$progress = $data['progress'] ?? 0;

$updateQuery = "INSERT INTO user_books (user_id, book_id, progress) VALUES (?, ?, ?) 
                ON DUPLICATE KEY UPDATE progress = ?";
$stmt = $conn->prepare($updateQuery);
if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit();
}
$stmt->bind_param("iiii", $userId, $bookId, $progress, $progress);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Progress saved successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save progress']);
}

$stmt->close();
$conn->close();
?>
