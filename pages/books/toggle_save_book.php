<?php
session_start();
include('../../includes/db.php');

// Validasi login dan ambil ID user
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['success' => false, 'message' => 'User not logged in.']));
}
$userId = $_SESSION['user_id'];

// Ambil parameter dari request
$data = json_decode(file_get_contents('php://input'), true);
$bookId = isset($data['bookId']) ? $data['bookId'] : 0;
$action = isset($data['action']) ? $data['action'] : 'save'; // Default action is save

// Validasi input
if ($bookId === 0) {
    die(json_encode(['success' => false, 'message' => 'Invalid book ID']));
}

// Cek apakah buku sudah disimpan
$checkSavedQuery = "SELECT * FROM saved_books WHERE user_id = ? AND book_id = ?";
$checkStmt = $conn->prepare($checkSavedQuery);
if ($checkStmt === false) {
    die(json_encode(['success' => false, 'message' => 'Error in query preparation: ' . $conn->error]));
}
$checkStmt->bind_param("ii", $userId, $bookId);
$checkStmt->execute();
$savedResult = $checkStmt->get_result();
$isSaved = $savedResult->num_rows > 0;

// Handle save or unsave book
if ($action === 'save' && !$isSaved) {
    // Simpan buku ke daftar
    $insertQuery = "INSERT INTO saved_books (user_id, book_id) VALUES (?, ?)";
    $insertStmt = $conn->prepare($insertQuery);
    if ($insertStmt === false) {
        die(json_encode(['success' => false, 'message' => 'Error in query preparation: ' . $conn->error]));
    }
    $insertStmt->bind_param("ii", $userId, $bookId);
    $insertStmt->execute();

    $response = ['success' => true, 'message' => 'Book saved successfully.'];
} elseif ($action === 'unsave' && $isSaved) {
    // Hapus buku dari daftar
    $deleteQuery = "DELETE FROM saved_books WHERE user_id = ? AND book_id = ?";
    $deleteStmt = $conn->prepare($deleteQuery);
    if ($deleteStmt === false) {
        die(json_encode(['success' => false, 'message' => 'Error in query preparation: ' . $conn->error]));
    }
    $deleteStmt->bind_param("ii", $userId, $bookId);
    $deleteStmt->execute();

    $response = ['success' => true, 'message' => 'Book unsaved successfully.'];
} else {
    $response = ['success' => false, 'message' => 'Action not performed'];
}

// Return response
echo json_encode($response);
?>
