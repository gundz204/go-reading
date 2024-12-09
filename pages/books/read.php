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
$query = "SELECT title, content, image_url, author FROM books WHERE id = ?";
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
    $author = $row['author']; // Ambil data author
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

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; display: flex; height: 100vh; background-color: #e3dfde; }

        .sidebar { width: 80px; background-color: #ffffff; border-right: 1px solid #ddd; display: flex; flex-direction: column; align-items: center; padding-top: 20px; position: fixed; height: 100vh;}
        .sidebar-icon { width: 40px; height: 40px; margin: 15px 0; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #6c757d; transition: background-color 0.3s ease, color 0.3s ease; }
        .sidebar-icon.active, .sidebar-icon:hover { background-color: #f0f4ff; color: #4b82f1; border-radius: 10px; }
        .sidebar-icon img { width: 24px; height: 24px; }

        .container {
            max-width: 500px;
        }


        .container h1 {
            background-color: white;
            border-radius: 25px;
            padding: 10px;
            font-size: 24px;
            font-weight: bold;
            text-transform: capitalize;
            color: #3a539b;
        }

        #story-container {
            background-color: white;
        }

        .save-icon-border {
            width: 50px;
            height: 50px;
            background-color: white;
            border-radius: 50%;
        }
        .heading img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 15px;
            border: 3px solid #3a539b;
        }

        .cover {
            margin-left: 20px;
            width: 100px; 
            height: 100px;
        }

        #story-container {
            padding: 4vh;
            text-align:justify;
            border-radius: 15px;
            font-weight: bold;
            color: #3a539b;
        }

        .author-text h1 {
            background-color: #ddd;
            padding: 1.2vh;
            width: 15vh;
        }

        @media (max-width: 768px) {
            body { flex-direction: column; }
            .sidebar { width: 100%; height: 60px; position: fixed; bottom: 0; left: 0; flex-direction: row; justify-content: space-around; padding-top: 0; border-top: 1px solid #ddd; z-index: 99; }
            .sidebar-icon { margin: 0; }
            .history-img { display: block; /* Show on mobile */ text-align: center; /* Center container */ }
            .history-img img { width: 70%; margin: auto; /* Center image */ display: block; }
            .container {
                margin: 0;
            }
        }

        
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-icon "><a href="../home.php"><img src="https://img.icons8.com/ios-filled/50/6c757d/home.png" alt="Home Icon" /></a></div>
        <div class="sidebar-icon"><a href="library.php"><img src="https://img.icons8.com/?size=100&id=59740&format=png&color=6c757d" alt="Library Icon" /></a></div>
        <div class="sidebar-icon active"><img src="https://img.icons8.com/ios-filled/50/4b82f1/time-machine.png" alt="History Icon" /></div>
        <div class="sidebar-icon"><a href="profile.php"><img src="https://img.icons8.com/ios-filled/50/6c757d/user.png" alt="profile Icon" /></a></div>
    </div>

    <div class="container mt-3 text-center w-100">
        <h1><?php echo htmlspecialchars($title); ?></h1>

        <!-- Save button with icon -->
        <div class="heading d-flex justify-content-between align-items-end p-2">
            <div class="cover d-flex gap-3">
                <div>
                    <img src="../../uploads/<?php echo htmlspecialchars($row['image_url']); ?>" alt="">
                </div>
                <div class="px-1 py-2 author-text">
                    <h1 style="font-size: 2.5vh; background-color: white;">author</h1>
                    <h1 style="font-size: 2.5vh;"><?php echo htmlspecialchars($author); ?></h1>
                </div>
            </div>
            <div id="save-icon" class="mb-3 save-icon-border col-6" style="font-size: 32px; cursor: pointer">
                <i class="bi <?php echo $isSaved ? 'bi-bookmark-check-fill' : 'bi-bookmark' ?>"></i>
            </div>
        </div>

        <div class="mt-3 mb-3" id="story-container" style="">
            <?php echo nl2br(htmlspecialchars($content)); ?>
        </div>

        <!-- Progress text -->
        <div class="mb-1" id="progress-text" style="display: none;"> 
            Progress: <?php echo $progress; ?>%
        </div>

        <!-- Navigation buttons -->
        <div class="d-flex justify-content-between">
            <button id="prevBtn" class="btn btn-outline d-none"><img src="https://img.icons8.com/?size=50&id=98961&format=png&color=000000" alt=""></i></button>
            <button id="nextBtn" class="btn btn-outline"><img src="https://img.icons8.com/?size=50&id=98968&format=png&color=000000" alt=""></i></button>
            <button id="finishBtn" class="btn btn-outline text-success fw-bold d-none btn-sm" data-bs-toggle="modal" data-bs-target="#successModal" ">Attempt Quiz</button>
        </div>

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
    <br><br><br><br>

<script>
  // Existing code
const storyContainer = document.getElementById("story-container");
const progressText = document.getElementById("progress-text");
const nextBtn = document.getElementById("nextBtn");
const finishBtn = document.getElementById("finishBtn");
const prevBtn = document.getElementById("prevBtn");
const saveIcon = document.getElementById("save-icon");

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
    const userConfirmation = confirm("Anda sudah menyelesaikan cerita ini. Apakah Anda ingin mengulang cerita?");
    if (userConfirmation) {
        progress = 0;
        currentPartIndex = 0;
        saveProgressToDatabase(progress);
        window.location.href = "read.php?id=<?php echo $bookId; ?>";
    }
}

// Event listener for Next button
nextBtn.addEventListener("click", function () {
    if (currentPartIndex < totalParts - 1) {
        currentPartIndex++;
        updateStory();
        saveProgressToDatabase(Math.round((currentPartIndex + 1) / totalParts * 100));
    }
});

// Event listener for Previous button
prevBtn.addEventListener("click", function () {
    if (currentPartIndex > 0) {
        currentPartIndex--;
        updateStory();
        saveProgressToDatabase(Math.round((currentPartIndex + 1) / totalParts * 100));
    }
});

// Event listener for Finish button
finishBtn.addEventListener("click", function () {
    if (currentPartIndex === totalParts - 1) {
        saveProgressToDatabase(100);
        window.location.href = "quiz.php?book_id=<?php echo $bookId; ?>";
    }
});

// Load initial story part based on progress
updateStory();

// Save/unsave book functionality
let isSaved = <?php echo json_encode($isSaved); ?>;

saveIcon.addEventListener("click", function () {
    const action = isSaved ? 'unsave' : 'save'; // If already saved, set action to unsave
    fetch('toggle_save_book.php', {
        method: 'POST',
        body: JSON.stringify({
            bookId: <?php echo $bookId; ?>,
            action: action
        }),
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log(data); // Log response for debugging
        if (data.success) {
            isSaved = !isSaved; // Toggle saved status
            saveIcon.innerHTML = isSaved
                ? '<i class="bi bi-bookmark-check-fill"></i> '
                : '<i class="bi bi-bookmark"></i> ';
        } else {
            alert("Failed to update save status.");
        }
    })
    .catch(error => console.error('Error:', error));
});

</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
