<?php
session_start();
// Sertakan file koneksi database
include('../../includes/db.php');

// Validasi login dan ambil ID user
if (!isset($_SESSION['user_id'])) {
    die('User not logged in.');
}
$userId = $_SESSION['user_id'];

// Tangkap parameter ID dari URL
$bookId = isset($_GET['id']) ? $_GET['id'] : 0;

// Query untuk mengambil data buku berdasarkan ID
$query = "SELECT title, content FROM books WHERE id = ?";
$stmt = $conn->prepare($query);
if ($stmt === false) {
    die('Error in query preparation: ' . $conn->error);
}
$stmt->bind_param("i", $bookId);
$stmt->execute();
$result = $stmt->get_result();

// Periksa apakah data buku ditemukan dan konten ada
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $title = $row['title'];
    $content = $row['content'];
    if (empty($content)) {
        echo '<p>Book content is empty.</p>';
        exit();
    }
} else {
    echo '<p>Book not found.</p>';
    exit();
}

// Periksa apakah buku sudah disimpan oleh pengguna
$checkSavedQuery = "SELECT * FROM saved_books WHERE user_id = ? AND book_id = ?";
$checkStmt = $conn->prepare($checkSavedQuery);
if ($checkStmt === false) {
    die('Error in query preparation: ' . $conn->error);
}
$checkStmt->bind_param("ii", $userId, $bookId);
$checkStmt->execute();
$savedResult = $checkStmt->get_result();
$isSaved = $savedResult->num_rows > 0;

// Periksa apakah progres baca sudah ada
$checkProgressQuery = "SELECT progress FROM user_books WHERE user_id = ? AND book_id = ?";
$checkProgressStmt = $conn->prepare($checkProgressQuery);
if ($checkProgressStmt === false) {
    die('Error in query preparation: ' . $conn->error);
}
$checkProgressStmt->bind_param("ii", $userId, $bookId);
$checkProgressStmt->execute();
$progressResult = $checkProgressStmt->get_result();
$progress = $progressResult->num_rows > 0 ? $progressResult->fetch_assoc()['progress'] : 0;

// Tutup koneksi database
$stmt->close();
$checkStmt->close();
$checkProgressStmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Baca Buku - <?php echo htmlspecialchars($title); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5 text-center w-100" style="max-width: 500px">
    <h1><?php echo htmlspecialchars($title); ?></h1>

    <!-- Save button with icon -->
    <div id="save-icon" class="mb-3" style="font-size: 24px; cursor: pointer">
        <i class="bi <?php echo $isSaved ? 'bi-heart-fill text-danger' : 'bi-heart' ?>"></i>
        <?php echo $isSaved ? 'Saved' : 'Save' ?>
    </div>

    <div class="mt-3 mb-3" id="story-container" style="height: 250px; text-align: left; background-color: azure">
        <?php echo nl2br(htmlspecialchars($content)); ?>
    </div>

    <!-- Progress text -->
    <div class="mb-3" id="progress-text">
        Progress: <?php echo $progress; ?>%
    </div>

    <!-- Navigation buttons -->
    <button id="prevBtn" class="btn btn-secondary d-none">Previous</button>
    <button id="nextBtn" class="btn btn-primary">Next</button>
    <button id="saveProgressBtn" class="btn btn-warning">Save Progress</button>
    <button id="finishBtn" class="btn btn-success d-none" data-bs-toggle="modal" data-bs-target="#successModal">Attempt Quiz</button>

    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Congratulations! You have completed reading the story.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="goToQuizBtn">Oke</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Initialize story parts and navigation
const storyContainer = document.getElementById("story-container");
const progressText = document.getElementById("progress-text");
const nextBtn = document.getElementById("nextBtn");
const finishBtn = document.getElementById("finishBtn");
const prevBtn = document.getElementById("prevBtn");
const saveProgressBtn = document.getElementById("saveProgressBtn");

const fullStory = <?php echo json_encode($content); ?>;
const maxPartLength = 500;
const storyParts = [];
for (let i = 0; i < fullStory.length; i += maxPartLength) {
    storyParts.push(fullStory.slice(i, i + maxPartLength));
}

const totalParts = storyParts.length;
let progress = <?php echo $progress; ?>;
let currentPartIndex = Math.floor(progress / 100 * totalParts);

// Initialize story parts and navigation
function updateStory() {
    // Update the content of the current part
    storyContainer.textContent = storyParts[currentPartIndex];

    // Calculate and display progress
    const progressPercentage = Math.min(((currentPartIndex + 1) / totalParts) * 100, 100);
    progressText.textContent = "Progress: " + Math.round(progressPercentage) + "%";

    // Toggle button visibility
    prevBtn.classList.toggle("d-none", currentPartIndex === 0);
    nextBtn.classList.toggle("d-none", currentPartIndex === totalParts - 1);
    finishBtn.classList.toggle("d-none", currentPartIndex !== totalParts - 1);

    // If the user reaches the last part, update progress to 100%
    if (currentPartIndex === totalParts - 1) {
        progress = 100;
    }
}

// Function to save progress to the database
function saveProgressToDatabase(progress) {
    fetch('update_progress.php', {
        method: 'POST',
        body: JSON.stringify({
            bookId: <?php echo $bookId; ?>,
            progress: progress
        }),
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .catch(error => console.error('Error:', error));
}

// Check if the user has already reached 100% progress
if (progress === 100) {
    // Show confirmation popup to restart the story
    const userConfirmation = confirm("Anda sudah menyelesaikan cerita ini. Apakah Anda ingin mengulang cerita?");
    if (userConfirmation) {
        // Reset progress to 0 and navigate to the start
        progress = 0;
        currentPartIndex = 0;
        saveProgressToDatabase(progress); // Save the reset progress to the database
        window.location.href = "read.php?book_id=<?php echo $bookId; ?>"; // Redirect to the start of the story
    }
}

// Event listener for Next button
nextBtn.addEventListener("click", function () {
    if (currentPartIndex < totalParts - 1) {
        currentPartIndex++;
        updateStory();
    }
});

// Event listener for Previous button
prevBtn.addEventListener("click", function () {
    if (currentPartIndex > 0) {
        currentPartIndex--;
    }
    updateStory();
});

// Event listener for Save Progress button
saveProgressBtn.addEventListener("click", function () {
    saveProgressToDatabase(Math.round((currentPartIndex + 1) / totalParts * 100));
    alert("Save progress sucessfully!");
});

// Event listener for Finish button
finishBtn.addEventListener("click", function () {
    // Save progress to 100% if the story is finished
    if (currentPartIndex === totalParts - 1) {
        saveProgressToDatabase(100); // Save the progress as 100%
        window.location.href = "quiz.php?book_id=<?php echo $bookId; ?>"; // Redirect to quiz page or another page
    }
});

// Load initial story part based on progress
updateStory();


</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
