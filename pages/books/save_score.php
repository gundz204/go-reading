<?php
session_start();
include('../../includes/db.php');

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['success' => false, 'message' => 'User not logged in']));
}

$userId = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);
$score = isset($data['score']) ? intval($data['score']) : 0;

$query = "UPDATE users SET points = ? WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $score, $userId);

$response = [];
if ($stmt->execute()) {
    $response['success'] = true;
} else {
    $response['success'] = false;
    $response['message'] = "Error updating score";
}

$stmt->close();
$conn->close();

echo json_encode($response);
